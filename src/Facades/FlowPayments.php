<?php

namespace Themey99\LaravelFlowPayments\Facades;

use Illuminate\Support\Facades\Facade;

class FlowPayments extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'flowPayments';
    }
}
