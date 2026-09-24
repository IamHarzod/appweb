<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;
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
        // Use Bootstrap pagination to prevent SVG oversized buttons
        Paginator::useBootstrapFive();

        View::composer(['layout.home_layout', 'layout.profile_layout', 'client.*'], function ($view) {
            try {
                if (Schema::hasTable('_category')) {
                    $categories = Cache::remember('global_categories_view', 3600, function () {
                        return Category::orderBy('id', 'desc')->get();
                    });
                    $view->with('categories', $categories);
                }
            } catch (\Throwable $e) {
                // Ignore during migrations or testing
            }
        });
    }
}
