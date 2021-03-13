<?php

namespace Themey99\LaravelFlowPayments\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Themey99\LaravelFlowPayments\Contracts\FlowPaymentsApiContract;
use Themey99\LaravelFlowPayments\Facades\FlowLog;

/**
 * Author: Flow CL
 */
class FlowPaymentsApi implements FlowPaymentsApiContract
{
    /**
     * Flow's Credentials
     * @var string
     */
    protected $apiKey, $secretKey, $baseUrl;

    public function __construct()
    {
        // API KEY
        $this->apiKey = config('flow.api.key');
        // SECRET KEY
        $this->secretKey = config('flow.api.secret');
        // BASE URL
        $this->baseUrl = config('flow.api.url');
    }

    /**
     * Method that invokes a Flow API service
     * 
     * @param string $method Http method to use
     * @param string $service Name of the service to be invoked
     * @param Collection $params Data to be sent
     * @return array In JSON format
     * @throws \Exception
     */
    public function send(string $method = "GET", string $service, Collection $params): array
    {
        $method = strtoupper($method);
        $url = $this->baseUrl . $service;

        // Prepare data to request
        $params = $params->merge(["apiKey" => $this->apiKey]);
        $data = $params->packFlow($method);
        $sign = $params->signFlow($this->secretKey);

        if ($method == "GET") {
            $response = $this->httpGet($url, $data, $sign);
        } else {
            $response = $this->httpPost($url, $data, $sign);
        }

        if (isset($response["info"])) {
            $code = $response["info"]["http_code"];
            $body = json_decode($response["output"], true);
            if ($code == "200") {
                return $body;
            } elseif (in_array($code, array("400", "401"))) {
                FlowLog::error("Failed the request to the uri {$url}", [
                    'code' => $body["code"],
                    'message' => $body["message"],
                    'data' => $params->toArray(),
                    'user' => Auth::id()
                ]);
                throw new \Exception($body["message"], $body["code"]);
            } else {
                FlowLog::error("Unexpected error occurred in request {$url}", [
                    'code' => $code,
                    'data' => $params->toArray(),
                    'user' => Auth::id()
                ]);
                throw new \Exception("Unexpected error occurred. HTTP_CODE: " . $code, $code);
            }
        } else {
            FlowLog::error("Unexpected error occurred {$url}", [
                'data' => $params->toArray(),
                'user' => Auth::id()
            ]);
            throw new \Exception("Unexpected error occurred.", 500);
        }
    }

    /**
     * Method that makes the call via http GET
     * 
     * @param string $url Url to invoke
     * @param string $data Data to send
     * @param string $sign Signature of the data
     * @return array In JSON format
     * @throws \Exception
     */
    private function httpGet(string $url, string $data, string $sign): array
    {
        $url = $url . "?" . $data . "&s=" . $sign;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($ch);
        if ($output === false) {
            $error = curl_error($ch);
            throw new \Exception($error, 1);
        }
        $info = curl_getinfo($ch);
        curl_close($ch);
        return array("output" => $output, "info" => $info);
    }

    /**
     * Method that makes the call via http POST
     * 
     * @param string $url Url to invoke
     * @param string $data Data to send
     * @param string $sign Signature of the data
     * @return string In JSON format
     * @throws \Exception
     */
    private function httpPost(string $url, string $data, string $sign): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //CURLOPT_SSL_VERIFYPEER => false
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data . "&s=" . $sign);
        $output = curl_exec($ch);
        if ($output === false) {
            $error = curl_error($ch);
            throw new \Exception($error, 1);
        }
        $info = curl_getinfo($ch);
        curl_close($ch);
        return array("output" => $output, "info" => $info);
    }
}
