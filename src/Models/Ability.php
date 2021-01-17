<?php

namespace Laranoia\Permissions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laranoia\Permissions\Contracts\Ability as AbilityContract;
use Laranoia\Permissions\Exceptions\AbilityDoesNotExist;

class Ability extends Model implements AbilityContract
{

    protected $fillable = ['name', 'display_name', 'description', 'created_at', 'updated_at'];

    public function getTable()
    {
        return config('permissions.tables.abilities', parent::getTable());
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('permissions.models.role'));
    }

    public static function findByName(string $name): AbilityContract
    {
        $role = static::where('name', $name)->first();

        if(!$role){
            throw AbilityDoesNotExist::named($name);
        }
    }
}