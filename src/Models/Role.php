<?php

namespace Laranoia\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laranoia\Permissions\Contracts\Role as RoleContract;
use Laranoia\Permissions\Exceptions\RoleDoesNotExist;
use \Parental\HasChildren;

class Role extends Model implements RoleContract
{

    use HasChildren;

    protected $childTypes = [];

    protected $childColumn = 'name';

    protected $fillable = ['name', 'display_name', 'created_at', 'updated_at'];

    public function getTable()
    {
        return config('permissions.tables.roles', parent::getTable());
    }

    public function permissions()
    {
        return $this->hasMany(
            config('permissions.models.permission'),
            'role_id',
            'id'
        );
    }

    public function abilities(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permissions.models.ability'),
            config('permissions.models.role_ability')
        );
    }

    /** Scope to check role validity */
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

    public static function findByName(string $name): RoleContract
    {
        $role = static::where('name', $name)->first();

        if (!$role) {
            throw RoleDoesNotExist::named($name);
        }
        return $role;
    }


    public function hasAbility($ability): bool
    {
        return $this->abilities->contains('name', $ability);
    }
}