<?php

namespace Themey99\LaravelFlowPayments\Contracts;

use Illuminate\Http\Request;

interface FlowPaymentsContract
{
    /**
     * Set the repository request to use.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return self
     */
    public function setRequest(Request $request): self;

    /**
     * Method to Merge data with order data
     * 
     * @param array $data
     * @return self
     */
    public function mergeOrder(array $data): self;

    /**
     * Method to set optional data
     * 
     * @param Collection|array $data
     * @return self
     */
    public function setOptionalData($data): self;

    /**
     * Method to set email data
     * 
     * @param string $data
     * @return self
     */
    public function setEmailData(string $email): self;

    /**
     * Method to set amount data
     * 
     * @param int|float $data
     * @return self
     */
    public function setAmountData($amount): self;

    /**
     * Method to Genearate a new Orden Payment
     * 
     * @return array
     */
    public function generateOrdenPayment(): array;

    /**
     * Method to Received Payment
     */
    public function receivedConfirmPayment();
}
