<?php

namespace App\Providers;

use App\Repositories\BaseRepository;
use App\Repositories\CourseRepository;
use App\Repositories\EnrollmentRepository;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\LessonRepositoryInterface;
use App\Repositories\Interfaces\ModuleRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\LessonRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);
        $this->app->bind(EnrollmentRepositoryInterface::class, EnrollmentRepository::class);
        $this->app->bind(ModuleRepositoryInterface::class, ModuleRepository::class);
        $this->app->bind(LessonRepositoryInterface::class, LessonRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
