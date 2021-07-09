<?php

namespace Themey99\LaravelFlowPayments\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade FlowPayments
 * 
 * @method setRequest
 * @method mergeOrder
 * @method setOptionalData
 * @method setEmailData
 * @method setAmountData
 * @method generateOrdenPayment
 * @method receivedConfirmPayment
 * @method getModel
 * 
 * @see \Themey99\LaravelFlowPayments\FlowPayments
 */
class FlowPayments extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'flowPayments';
    }
}
