<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Blade;

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
        // $accessControlInstance = AccessControlService::getInstance();

        Blade::if('canAccess', function($expression) {
            eval("\$params = [$expression];");
            list($permissions) = $params;
    
            return access_control()->canAccess(auth()->user(), $permissions);
        });
    }
}
