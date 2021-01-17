<?php

namespace Laranoia\Permissions\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Laranoia\Permissions\Contracts\Permission as PermissionContract;
use Laranoia\Permissions\Scopes\ValidPermissionScope;

class Permission extends Pivot implements PermissionContract
{
    protected static function booted()
    {
        static::addGlobalScope(new ValidPermissionScope);
    }

    public function getTable()
    {
        return config('permissions.tables.permissions', parent::getTable());
    }
/*
    public function scopeValid($query)
    {
        return $query
            ->where(function ($query) {
                return $query->where('valid_from', null)->orWhere('valid_from', '<', new \DateTimeImmutable());
            })
            ->where(function ($query) {
                return $query->where('valid_until', null)->orWhere('valid_until', '>', new \DateTimeImmutable());
            });
    }
*/
}