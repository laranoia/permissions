<?php

namespace Laranoia\Permissions\Events;

use Illuminate\Contracts\Auth\Access\Authorizable;

class UserValidated
{
    /** @var Authorizable */
    public $user;

    public function __construct(Authorizable $user){
        $this->user = $user;
    }
}