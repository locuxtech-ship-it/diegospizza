<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use App\Models\Tenant;
use App\Services\PlanService;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoginResponseContract::class, LoginResponse::class);

        $this->app->scoped(PlanService::class, function ($app) {
            $tenant = $app->bound(Tenant::class) ? $app->make(Tenant::class) : null;
            return (new PlanService)->forTenant($tenant);
        });
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        Gate::define('applyDiscount', fn ($user) => $user->isAdmin());
        Gate::define('usePlanFeature', function ($user, string $feature) {
            return app(PlanService::class)->can($feature);
        });
    }
}
