<?php

namespace Laranoia\Permissions;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laranoia\Permissions\Commands\Import;
use Laranoia\Permissions\Contracts\Permission as PermissionContract;
use Laranoia\Permissions\Contracts\Ability as AbilityContract;
use Laranoia\Permissions\Contracts\Role as RoleContract;
use Laranoia\Permissions\Events\UserValidated;
use Laranoia\Permissions\Listeners\UserLogin;

class PermissionServiceProvider extends ServiceProvider
{

    /** @var \string[][]  */
    protected $listen = [
        Login::class => [
            UserLogin::class
        ],
        UserValidated::class => [

        ]
    ];

    public function boot(PermissionManager $permissionManager)
    {
        $this->publishes([
            __DIR__ . '/../config/permissions.php' => config_path('permissions.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->commands([
            Import::class
        ]);

        $this->app->singleton(PermissionManager::class, function ($app) use ($permissionManager) {
            return $permissionManager;
        });

        $this->registerPermissions($permissionManager);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            $this->app->configPath('permissions.php'),
            'permissions'
        );

        $this->app->bind(PermissionContract::class, $this->app->config['permissions.models.permission']);
        $this->app->bind(AbilityContract::class, $this->app->config['permissions.models.ability']);
        $this->app->bind(RoleContract::class, $this->app->config['permissions.models.role']);

        $this->booting(function(){
            foreach ($this->listen as $event => $listeners){
                foreach (array_unique($listeners) as $listener){
                    Event::listen($event, $listener);
                }
            }
        });
    }


    /**
     * Register the permission check method on the gate.
     */
    protected function registerPermissions(PermissionManager $permissionManager)
    {
        Gate::before(function (Authorizable $user, string $ability) use($permissionManager) {
            return $permissionManager->hasAbility($ability);
        });
    }
}