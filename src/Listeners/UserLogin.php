<?php

namespace Laranoia\Permissions\Listeners;

use Illuminate\Auth\Events\Login;
use Laranoia\Permissions\PermissionManager;

class UserLogin
{
    public function handle(Login $event)
    {
        app(PermissionManager::class)->handleLogin($event);
    }
}