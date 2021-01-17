<?php

namespace Laranoia\Permissions\Listeners;

use Illuminate\Auth\Events\Login;
use Laranoia\Permissions\PermissionManager;

class UserValidated
{
    public function handle(UserValidated $event)
    {
        $user = $event->user;
        $user->loadRoles();
        \Auth::user()->loadRoles();
    }
}