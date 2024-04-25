<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Game;
use App\Models\Mod;
use App\Policies\GamePolicy;
use App\Policies\ModPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Game::class => GamePolicy::class,
        Mod::class => ModPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
