<?php

namespace App\Providers;

use App\Interfaces\Services\CategoryServiceInterface;
use App\Interfaces\Services\CourseServiceInterface;
use App\Interfaces\Services\ModuleServiceInterface;
use App\Interfaces\Services\UserServiceInterface;
use App\Services\CategoryService;
use App\Services\CourseService;
use App\Services\ModuleService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(CourseServiceInterface::class, CourseService::class);
        $this->app->bind(ModuleServiceInterface::class, ModuleService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
