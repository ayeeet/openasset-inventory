<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share currency symbol and app_logo with all views
        view()->composer('*', function ($view) {
            $settings = \App\Models\Setting::whereIn('key', ['currency_symbol', 'app_logo', 'usd_to_php_rate'])->pluck('value', 'key');
            $view->with('currency', $settings['currency_symbol'] ?? '$');
            $view->with('app_logo', $settings['app_logo'] ?? null);
            $view->with('usd_to_php_rate', isset($settings['usd_to_php_rate']) ? (float) $settings['usd_to_php_rate'] : 59);
        });
    }
}
