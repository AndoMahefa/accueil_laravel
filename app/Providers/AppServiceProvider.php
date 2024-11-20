<?php

namespace App\Providers;

use App\Services\ServiceManager;
use App\Services\TicketService;
use App\Services\VisiteurService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {
        $this->app->singleton(ServiceManager::class, function ($app) {
            return new ServiceManager();
        });

        $this->app->singleton(VisiteurService::class, function ($app) {
            return new VisiteurService();
        });

        $this->app->singleton(TicketService::class, function ($app) {
            return new TicketService();
        }); 
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
