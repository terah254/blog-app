<?php

namespace App\Providers;

use App\Models\Blog;
use App\Observers\BlogObserver;
use App\Policies\BlogPolicy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Register policies
        Gate::policy(Blog::class, BlogPolicy::class);

        // Register observers
        Blog::observe(BlogObserver::class);

        // Share stats with specific views (cached for 5 minutes)
        View::composer(['home', 'admin.dashboard'], function ($view) {
            $stats = Cache::remember('app_stats', 300, function () {
                return [
                    'total_blogs' => Blog::count(),
                    'published_blogs' => Blog::where('status', 'published')->count(),
                    'total_users' => \App\Models\User::where('role', 'user')->count(),
                    'total_comments' => \App\Models\Comment::count(),
                ];
            });

            $view->with('stats', $stats);
        });
    }
}
