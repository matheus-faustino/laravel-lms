<?php

namespace App\Providers;

use App\Services\AuthenticationService;
use App\Services\BaseService;
use App\Services\CourseService;
use App\Services\EnrollmentService;
use App\Services\Interfaces\AuthenticationServiceInterface;
use App\Services\Interfaces\BaseServiceInterface;
use App\Services\Interfaces\CourseServiceInterface;
use App\Services\Interfaces\EnrollmentServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\UserService;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
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
        $this->app->bind(CourseServiceInterface::class, CourseService::class);
        $this->app->bind(EnrollmentServiceInterface::class, EnrollmentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('Bearer')
                );
            });
    }
}
