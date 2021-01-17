<?php

namespace Laranoia\Permissions\Traits;

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
        // also grouping wherePivot() is currently not possible -> manually targeting the columns
        $permissionsTable = config('permissions.tables.permissions');

        $query = $this->roles()->orderBy('priority')->getQuery();

        (new ValidPermissionScope())->apply($query, $this);
        $res = $query;
            //$this->roles()
            //    ->where(function ($query) use ($permissionsTable) {
            //        return $query->where($permissionsTable . '.valid_from', null)->orWhere($permissionsTable . '.valid_from', '<', new \DateTimeImmutable());
            //    })
            //    ->where(function ($query) use ($permissionsTable) {
            //        return $query->where($permissionsTable . '.valid_until', null)->orWhere($permissionsTable . '.valid_until', '>', new \DateTimeImmutable());
            //    })
            //    ->orderBy('priority');
        dump($res->toSql());
        return $res;
    }

    public function validPermissions()
    {
        return $this->permissions()->valid();
    }

    public function hasAbility(string $ability)
    {
        return app(PermissionManager::class)->hasAbility($ability);
    }

    public function login(){

        $validRoles = $this->validRoles()->with('abilities')->get()->keyBy('name');

        if($validRoles->isEmpty()){
            //TODO throw no role
        }
        if($this->last_role && $validRoles->has($this->last_role)){
            $logonRole = $validRoles->get($this->last_role);
        }
        else{
            $logonRole = $validRoles->first();
        }
        $this->currentRole = $logonRole;

        $this->last_login = new \DateTimeImmutable();
        $this->last_role = $this->currentRole->name;
        $this->save();
    }

    public function __get($name)
    {
        if($name === 'currentRole'){
            return $this->currentRole;
        }
        return parent::__get($name);
    }
}