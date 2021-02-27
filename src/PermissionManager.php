<?php

namespace Laranoia\Permissions;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Events\Dispatcher;
use Laranoia\Permissions\Events\PermissionsLoaded;
use Laranoia\Permissions\Events\UserValidated;

class PermissionManager
{
    /** @var Dispatcher */
    protected $events;

    /** @var Authorizable */
    protected $user;

    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    public function handleLogin(Login $event)
    {
        $this->user = $event->user;

        $this->user->validate();
        $this->events->dispatch(new UserValidated($this->user));

        $this->user->login();
        $this->events->dispatch(new PermissionsLoaded());;
    }

    public function hasAbility(string $ability){
        return $this->user->hasAbility($ability);
    }
}