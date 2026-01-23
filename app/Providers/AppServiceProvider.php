<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ApiService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ApiService::class, function ($app) {
            return new ApiService();
        });
    }

    public function boot()
    {
        // Compartir datos con todas las vistas
        view()->composer('*', function ($view) {
            $apiService = app(ApiService::class);
            $view->with('user', $apiService->getUser());
            $view->with('isAuthenticated', $apiService->isAuthenticated());
            $view->with('userRole', $apiService->getUserRole());
        });
    }
}