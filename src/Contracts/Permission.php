<?php

namespace Laranoia\Permissions\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Permission{

    #public function user(): Authenticatable;

    #public function role(): HasOne;

    #public function grantedTo(): MorphMany;

    #public function permissionType();


}