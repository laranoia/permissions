<?php

namespace Laranoia\Permissions\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ValidPermissionScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder
            ->where(function ($query) {
                return $query->where('valid_from', null)->orWhere('valid_from', '<', new \DateTimeImmutable());
            })
            ->where(function ($query) {
                return $query->where('valid_until', null)->orWhere('valid_until', '>', new \DateTimeImmutable());
            });
    }
}
