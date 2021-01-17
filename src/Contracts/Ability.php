<?php

namespace Laranoia\Permissions\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Ability{

    public static function findByName(string $name): self;

    public function roles(): BelongsToMany;

}