<?php

namespace Laranoia\Permissions\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Role{

    public static function findByName(string $name): self;

    public function abilities(): BelongsToMany;

    public function hasAbility($ability): bool;

}