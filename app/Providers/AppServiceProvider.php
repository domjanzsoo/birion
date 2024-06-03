<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Blade;
use App\Services\AccessControlService;

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
        $accessControlInstance = AccessControlService::getInstance();

        Blade::if('canAccess', function($expression) use ($accessControlInstance) {
            eval("\$params = [$expression];");
            list($permissions) = $params;
    
            return $accessControlInstance->canAccess($permissions, auth()->user());
        });
    }
}
