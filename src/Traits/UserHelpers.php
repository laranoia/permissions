<?php

namespace Laranoia\Permissions\Traits;

use Laranoia\Permissions\Exceptions\NoValidRoles;
use Laranoia\Permissions\PermissionManager;
use Laranoia\Permissions\Scopes\ValidPermissionScope;

trait UserHelpers
{
    protected $currentRole;

    public function roles()
    {
        return $this->belongsToMany(
            config('permissions.models.role'),
            config('permissions.models.permission'),
            'user_id',
            'role_id'
        )
            ->as('permission')
            ->withPivot('valid_from', 'valid_until')
            ->withTimestamps()
            ->using(config('permissions.models.permission'));
    }

    public function permissions()
    {
        return $this->hasMany(
            config('permissions.models.permission'),
            'user_id',
            'id'
        );
    }

    public function validRoles()
    {
        // laravel doesn't seem to have a way to apply a scope to the pivot.
        // so checking the validity has to be done here
        // also grouping wherePivot() is currently not possible -> manually targeting the columns in the scope
        $query = $this->roles()->orderBy('priority')->getQuery();
        (new ValidPermissionScope())->apply($query, $this);
        return $query;
    }

    public function validPermissions()
    {
        return $this->permissions()->valid();
    }

    public function hasAbility(string $ability)
    {
        $this->currentRole->hasAbility($ability);
    }

    public function login()
    {
        $validRoles = $this->validRoles()->with('abilities')->get()->keyBy('name');

        if ($validRoles->isEmpty()) {
            throw new NoValidRoles('The user has no valid roles');
        }
        if ($this->last_role && $validRoles->has($this->last_role)) {
            $logonRole = $validRoles->get($this->last_role);
        } else {
            $logonRole = $validRoles->first();
        }
        $this->currentRole = $logonRole;

        $this->last_login = new \DateTimeImmutable();
        $this->last_role = $this->currentRole->name;
        $this->save();
    }

    public function __get($name)
    {
        if ($name === 'currentRole') {
            return $this->currentRole;
        }
        return parent::__get($name);
    }
}