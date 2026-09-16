<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

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
        View::composer(['layout.home_layout', 'layout.profile_layout', 'client.*'], function ($view) {
            try {
                if (Schema::hasTable('_category')) {
                    $categories = Category::orderBy('id', 'desc')->get();
                    $view->with('categories', $categories);
                }
            } catch (\Throwable $e) {
                // Ignore during migrations or testing
            }
        });
    }
}
