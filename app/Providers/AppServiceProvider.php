<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Services\ElasticsearchService;
use Illuminate\Support\ServiceProvider;
use App\Models\PostCollection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ElasticsearchService::class, function ($app) {
            return new ElasticsearchService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

        /* if (!Cache::has('posts_list_chunk_1')) {
            PostCollection::updateCache();
        } */
    }
}
