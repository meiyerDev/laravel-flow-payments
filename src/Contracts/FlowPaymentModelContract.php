<?php

namespace Themey99\LaravelFlowPayments\Contracts;

use Illuminate\Support\Collection;

interface FlowPaymentModelContract
{
    public function findByCommerceOrder(string $commerceOrder, $thowException = true);

    public function createFromOrder(Collection $order): FlowPaymentModelContract;

    public function updateFromConfirmation(array $order): FlowPaymentModelContract;
}
