<?php

namespace Themey99\LaravelFlowPayments;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Themey99\LaravelFlowPayments\Contracts\FlowPaymentModelContract;
use Themey99\LaravelFlowPayments\Contracts\FlowPaymentsApiContract;
use Themey99\LaravelFlowPayments\Contracts\FlowPaymentsContract;
use Themey99\LaravelFlowPayments\Services\FlowPaymentsApi;

class FlowPaymentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (function_exists('config_path')) { // function not available and 'publish' not relevant in Lumen
            $this->registerConfig();
            $this->registerMigrations();
        }

        $this->registerCollectionMacros();
        $this->registerBindings();

        if (Str::contains(config('app.url'), 'https://') && Str::contains(url(''), 'http://')) {
            URL::forceScheme('https');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/flow.php',
            'flow'
        );
        $this->loadMigrationsFrom(__DIR__ . '/Migrations/');
    }

    protected function registerBindings()
    {
        if ($this->app->config['flow.model']) {
            $this->app->bind(FlowPaymentModelContract::class, $this->app->config['flow.model']);
        }

        $this->app->bind(FlowPaymentsApiContract::class, FlowPaymentsApi::class);
        $this->app->bind(FlowPaymentsContract::class, $this->app->config['flow.flow_payments_class']);
        $this->app->bind('flowPayments', $this->app->config['flow.flow_payments_class']);
        $this->app->bind('flowLog', FlowPaymentsLog::class);
    }

    public function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../config/flow.php' => config_path('flow.php'),
        ], 'config');
    }

    public function registerMigrations()
    {
        if (!class_exists('CreateFlowPaymentsTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_flow_payments_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_flow_payments_table.php'),
            ], 'migrations');
        }
    }

    public function registerCollectionMacros()
    {
        if (!Collection::hasMacro('signFlow')) {
            Collection::macro('signFlow', function ($sign) {
                if (!function_exists("hash_hmac")) {
                    throw new \Exception("function hash_hmac not exist", 1);
                }

                return hash_hmac(
                    'sha256',
                    $this->map(function ($item, $key) {
                        $newItem = $item;
                        return "{$key}={$newItem}";
                    })->sortKeys()->join('&'),
                    $sign
                );
            });
        }

        if (!Collection::hasMacro('packFlow')) {
            Collection::macro('packFlow', function ($method = "GET") {
                if (!function_exists("hash_hmac")) {
                    throw new \Exception("function hash_hmac not exist", 1);
                }

                return $this->map(function ($item, $key) use ($method) {
                    if ($method == "GET") {
                        $newItem = rawurlencode($key) . "=" . rawurlencode($item);
                    } else {
                        $newItem = "{$key}={$item}";
                    }
                    return $newItem;
                })->sortKeys()->join('&');
            });
        }
    }
}
