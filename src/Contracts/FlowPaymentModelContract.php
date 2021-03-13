<?php

namespace Themey99\LaravelFlowPayments\Contracts;

use Illuminate\Support\Collection;
use Themey99\LaravelFlowPayments\Models\FlowPaymentModel;

interface FlowPaymentModelContract
{
    public function findByCommerceOrder(string $commerceOrder, $thowException = true);

    public function createFromOrder(Collection $order): FlowPaymentModel;

    public function updateFromConfirmation(array $order): self;
}
