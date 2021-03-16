<?php

namespace Themey99\LaravelFlowPayments;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Themey99\LaravelFlowPayments\Contracts\FlowPaymentModelContract;
use Themey99\LaravelFlowPayments\Contracts\FlowPaymentsApiContract;
use Themey99\LaravelFlowPayments\Contracts\FlowPaymentsContract;
use Illuminate\Support\Str;
use Themey99\LaravelFlowPayments\Facades\FlowLog;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class FlowPayments implements FlowPaymentsContract
{
    /**
     * @var FlowPaymentsApiContract
     */
    protected FlowPaymentsApiContract $flowApi;

    /**
     * @var FlowPaymentModelContract
     */
    protected FlowPaymentModelContract $flowPaymentModelContract;

    /**
     * @var Collection
     */
    protected $order;

    /**
     * Data optional to urls
     * 
     * @var string
     */
    protected $optionalConfirmation, $optionalReturn;

    /**
     * @var Request
     */
    protected $request;

    //Constructor de la clase
    function __construct(FlowPaymentsApiContract $flowApi, FlowPaymentModelContract $flowPaymentModelContract)
    {
        // SET DEFAULT ORDER
        $this->order = collect([
            "commerceOrder" => (string) (intval(app()->version()) > 6 ? Str::orderedUuid() : Str::uuid()),
            "subject" => "",
            "currency" => config('flow.currency'),
            "amount" => "",
            "paymentMethod" => config('flow.method_payment'),
            "email" => "",
            "optional" => null,
            "urlConfirmation" => $this->generateUrl(config('flow.urls.url_confirmation')),
            "urlReturn" => $this->generateUrl(config('flow.urls.url_return')),
        ]);

        // SET FLOW API SERVICE
        $this->flowApi = $flowApi;

        // SET MODEL FLOW CONTRACT
        $this->flowPaymentModelContract = $flowPaymentModelContract;

        // SET REQUEST
        $this->setRequest(request());
    }

    /**
     * Method to Set request
     *
     * @param \Illuminate\Http\Request $request
     * @return self
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Method to Merge data with order data
     * 
     * @param array $data
     * @return self
     */
    public function mergeOrder(array $data): self
    {
        $this->order = $this->order->merge($data);

        return $this;
    }

    /**
     * Method to set optional data
     * 
     * @param Collection|array $data
     * @return self
     */
    public function setOptionalData($data): self
    {
        if (is_array($data)) $data = collect($data);

        $this->mergeOrder([
            'optional' => $data->toJson()
        ]);

        return $this;
    }

    /**
     * Method to set email data
     * 
     * @param string $data
     * @return self
     */
    public function setEmailData(string $email): self
    {
        $this->mergeOrder([
            'email' => $email
        ]);

        return $this;
    }

    /**
     * Method to set amount data
     * 
     * @param int|float $data
     * @return self
     */
    public function setAmountData($amount): self
    {
        $this->mergeOrder([
            'amount' => $amount
        ]);

        return $this;
    }

    /**
     * Method to Generate a new Orden Payment
     * 
     * @return array
     */
    public function generateOrdenPayment(): array
    {
        $response = $this->flowApi->send(
            "POST",
            '/payment/create',
            $this->order,
        );

        $addDataToResponse = [
            'urlRedirect' => $response['url'] . "?token=" . $response["token"],
            'order' => $this->order,
        ];

        if (config('flow.model')) {
            $addDataToResponse['model'] = $this->flowPaymentModelContract->createFromOrder(
                $this->order->merge([
                    'urlRedirect' => $addDataToResponse['urlRedirect'],
                    'flowOrder' => $response['flowOrder']
                ])
            );
        }

        return array_merge($response, $addDataToResponse);
    }

    /**
     * Method to Received Payment
     * 
     * @throws \Exception
     */
    public function receivedConfirmPayment()
    {
        if ($this->request->missing('token')) {
            FlowLog::error("Missing token in request");
            throw new \Exception("Missing token in request", 400);
        }

        $data['response'] = $this->flowApi->send(
            'GET',
            '/payment/getStatus',
            collect($this->request->only('token'))
        );

        $data['model'] = $this->flowPaymentModelContract->updateFromConfirmation($data['response']);

        return $data;
    }

    /**
     * Method to generate Url from config
     * 
     * @param array|string $urlData
     * @throws \Exception
     */
    private function generateUrl($urlData)
    {
        if (is_array($urlData)) {
            if (array_key_exists('type', $urlData)) {
                if ($urlData['type'] == 'url') {
                    return url($urlData['name']);
                } elseif ($urlData['type'] == 'route') {
                    return route($urlData['name']);
                } elseif ($urlData['type'] == 'action') {
                    return action($urlData['name']);
                }
            }
        } elseif (is_string($urlData)) {
            return $urlData;
        }

        FlowLog::error("url not set correctly", [
            'url' => $urlData
        ]);

        throw new \Exception("url not set correctly", 500);
    }
}
