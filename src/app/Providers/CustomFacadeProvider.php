<?php

namespace App\Providers;

use App\Actions\ActivityLogger;
use App\Auth\Permissions\PermissionService;
use App\Theming\ThemeService;
use App\Uploads\ImageService;
use Illuminate\Support\ServiceProvider;

class CustomFacadeProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('activity', function () {
            return $this->app->make(ActivityLogger::class);
        });

        // $this->app->singleton('images', function () {
        //     return $this->app->make(ImageService::class);
        // });

        $this->app->singleton('permissions', function () {
            return $this->app->make(PermissionService::class);
        });

        // $this->app->singleton('theme', function () {
        //     return $this->app->make(ThemeService::class);
        // });
    }
}
