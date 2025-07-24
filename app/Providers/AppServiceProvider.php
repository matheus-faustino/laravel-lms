<?php

namespace App\Providers;

use App\Services\AuthenticationService;
use App\Services\BaseService;
use App\Services\Interfaces\AuthenticationServiceInterface;
use App\Services\Interfaces\BaseServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BaseServiceInterface::class, BaseService::class);

        $this->app->bind(AuthenticationServiceInterface::class, AuthenticationService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
