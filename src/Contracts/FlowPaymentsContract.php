<?php

namespace Themey99\LaravelFlowPayments\Contracts;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface FlowPaymentsContract
{
    /**
     * Set the repository request to use.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return self
     */
    public function setRequest(Request $request): FlowPaymentsContract;

    /**
     * Method to Merge data with order data
     * 
     * @param array $data
     * @return self
     */
    public function mergeOrder(array $data): FlowPaymentsContract;

    /**
     * Method to set optional data
     * 
     * @param Collection|array $data
     * @return self
     */
    public function setOptionalData($data): FlowPaymentsContract;

    /**
     * Method to set email data
     * 
     * @param string $data
     * @return self
     */
    public function setEmailData(string $email): FlowPaymentsContract;

    /**
     * Method to set amount data
     * 
     * @param int|float $data
     * @return self
     */
    public function setAmountData($amount): FlowPaymentsContract;

    /**
     * Method to get Model generated
     * 
     * @return FlowPaymentModelContract
     */
    public function getModel(): FlowPaymentModelContract;

    /**
     * Method to Genearate a new Orden Payment
     * 
     * @return Collection
     */
    public function generateOrdenPayment(): Collection;

    /**
     * Method to Received Payment
     */
    public function receivedConfirmPayment();
}
