<?php

namespace Themey99\LaravelFlowPayments;

class FlowPaymentsLog
{
    /**
     * Method to register the Log actions Failed
     *
     * @param string $message Message will write in log
     * @param array $context Context from Error
     */
    public function error(string $message, array $context = []): void
    {
        $this->log("ERROR", $message, $context);
    }

    /**
     * Method to register the Log actions
     *
     * @param string $type Message identifier
     * @param string $message Message will write in log
     * @param array $context Context from Log
     *
     */
    protected function log(string $type, string $message, array $context = [])
    {
        if (count($context)) {
            $message = $message . " " . json_encode($context);
        }

        $file = fopen(config('flow.logs.path') . "/" . config('flow.logs.name', 'flowLog') . "_" . date("Y-m-d") . ".log", "a+");

        fwrite(
            $file,
            "[" . date("Y-m-d H:i:s") . "] " . config('app.env') . ".{$type}: " . $message . PHP_EOL
        );

        fclose($file);
    }
}
