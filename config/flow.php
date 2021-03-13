<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enter here the table name of DB
    |--------------------------------------------------------------------------
    */
    'api' => [
        'key' => env('FLOW_API_ACCESS_KEY_ID'),
        'secret' => env('FLOW_API_SECRET_ACCESS_KEY'),
        'url' => env('FLOW_API_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Enter here the table name of DB
    |--------------------------------------------------------------------------
    */
    'table_name' => 'flow_payments',

    /*
    |--------------------------------------------------------------------------
    | Enter here the model class
    |--------------------------------------------------------------------------
    */
    'model' => \Themey99\LaravelFlowPayments\Models\FlowPaymentModel::class,

    'flow_payments_class' => \Themey99\LaravelFlowPayments\FlowPayments::class,

    /*
    |--------------------------------------------------------------------------
    | Enter here the method payment
    |--------------------------------------------------------------------------
    |
    | Allow values:
    | Only Webpay = 1
    | Only Servipag = 2
    | Only Multicaja = 3
    | All method payments = 9
    |
    */
    'method_payment' => 1,

    /*
    |--------------------------------------------------------------------------
    | Enter here the default currency (only used if not customized)
    |--------------------------------------------------------------------------
    */
    'currency' => 'CLP',

    /*
    |--------------------------------------------------------------------------
    | Enter here the url of our page
    |--------------------------------------------------------------------------
    |
    | Allow values:
    | 'https://www.commerce.com/endpoint',
    | ['type' => 'url', 'name' => 'flow/exito'],
    | ['type' => 'route', 'name' => 'flow.exito'],
    | ['type' => 'action', 'name' => 'FlowController@exito'],
    |
    */
    'urls' => [
        'url_confirmation' => [
            'type' => 'route',
            'name' => 'flow.confirmation'
        ],
        'url_return' => [
            'type' => 'route',
            'name' => 'flow.return'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Enter here the Log Data of It'll write the logs
    |--------------------------------------------------------------------------
    */
    'logs' => [
        'path' => storage_path('logs'),
        'name' => 'flowLog',
    ]
];
