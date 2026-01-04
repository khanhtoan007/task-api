<?php

namespace App\Providers;

use App\Contracts\QueryBuilderInterface;
use App\Models\ProjectMember;
use App\Policies\ProjectMemberPolicy;
use App\Services\QueryBuilder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use L5Swagger\L5SwaggerServiceProvider;
use NunoMaduro\Collision\Provider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(L5SwaggerServiceProvider::class);
        }
        (new Provider)->register();

        $this->app->bind(QueryBuilderInterface::class, QueryBuilder::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Gate::policy(ProjectMember::class, ProjectMemberPolicy::class);
    }
}
