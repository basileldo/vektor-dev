<?php

namespace Vektor\Checkout;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Vektor\Checkout\Http\Middleware\CheckoutModuleEnabled;
use Vektor\Checkout\Http\Middleware\CheckoutOnlyDisabled;

class CheckoutServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('checkout_module_enabled', CheckoutModuleEnabled::class);
        $router->aliasMiddleware('checkout_only_disabled', CheckoutOnlyDisabled::class);

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('checkout.php'),
            ], 'checkout');
        }

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'checkout');
    }
}
