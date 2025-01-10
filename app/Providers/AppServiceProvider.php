<?php

namespace App\Providers;

use App\Services\ServiceManager;
use App\Services\TicketService;
use App\Services\VisiteurService;
use App\Services\RendezVousService;
use App\Services\CreneauServiceManager;
use App\Services\AppelOffreService;
use App\Services\AppelOffreChampsService;
use App\Services\EmployeService;
use App\Services\UserService;
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

        $this->app->singleton(RendezVousService::class, function ($app) {
            return new RendezVousService();
        });

        $this->app->singleton(CreneauServiceManager::class, function($app) {
            return new CreneauServiceManager();
        });

        $this->app->singleton(AppelOffreService::class, function($app) {
            return new AppelOffreService();
        });

        $this->app->singleton(AppelOffreChampsService::class, function($app) {
            return new AppelOffreChampsService();
        });

        $this->app->singleton(EmployeService::class, function($app) {
            return new EmployeService();
        });

        $this->app->singleton(UserService::class, function($app) {
            return new UserService();
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
