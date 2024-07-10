<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contract\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Contract\PermissionRepositoryInterface;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use App\Contract\RoleRepositoryInterface;
use App\Repositories\PropertyRepository;
use App\Contract\PropertyRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PropertyRepositoryInterface::class, PropertyRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
