<?php

namespace Themey99\LaravelFlowPayments\Contracts;

use Illuminate\Support\Collection;

interface FlowPaymentsApiContract
{
    /**
     * Method that invokes a Flow API service
     * 
     * @param string $method Http method to use
     * @param string $service Name of the service to be invoked
     * @param Collection $params Data to be sent
     * @return array In JSON format
     * @throws \Exception
     */
    public function send(string $method = "GET", string $service, Collection $params): array;
}
