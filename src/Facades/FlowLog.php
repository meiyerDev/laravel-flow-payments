<?php

namespace Themey99\LaravelFlowPayments\Facades;

use Illuminate\Support\Facades\Facade;

class FlowLog extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'flowLog';
    }
}
