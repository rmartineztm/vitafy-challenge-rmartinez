<?php

namespace App\Providers;

use App\Contracts\LeadScoringServiceInterface;
use App\Services\LeadScoringService;
use Illuminate\Http\Resources\Json\JsonResource;
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
        $this->app->bind(LeadScoringServiceInterface::class, config('iahorro.lead_scoring_service_class'));
        JsonResource::withoutWrapping();
    }
}
