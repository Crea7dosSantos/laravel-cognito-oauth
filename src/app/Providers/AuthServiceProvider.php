<?php

namespace App\Providers;

use App\Models\Client;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }

        Route::get('oauth/authorize', [
            'uses' => '\App\Http\Controllers\OAuth\CustomAuthorizationController@authorize',
        ])->middleware(['web', 'auth']);

        Passport::useClientModel(Client::class);
        Passport::tokensExpireIn(now()->addMinute(30));
        Passport::refreshTokensExpireIn(now()->addMonths(1));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
