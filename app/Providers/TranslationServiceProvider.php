<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\TranslationRepositoryInterface;
use App\Repositories\TranslationRepository;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // ensure to bound TranslationRepositoryInterface to TranslationRepository
        $this->app->bind(TranslationRepositoryInterface::class, TranslationRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
