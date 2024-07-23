<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Policies\UserPolicy;
use App\Models\User;
use Illuminate\Support\Facades\Gate;



class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        'App\Models\User' => 'App\Policies\UserPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->registerPolicies();

        // Additional policy definitions using Gate::define
        Gate::define('edit-user', [UserPolicy::class, 'update']);
        Gate::define('delete-user', [UserPolicy::class, 'delete']);
    }
}
