<?php

namespace Laranoia\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Laranoia\Permissions\Contracts\RoleAbility as RoleAbilityContract;

class RoleAbility extends Model implements RoleAbilityContract
{
    public function getTable()
    {
        return config('permissions.tables.roles_abilities', parent::getTable());
    }
}