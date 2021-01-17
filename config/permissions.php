<?php

return [

    'cache' => [
        'key' => 'laranoia.permissions.cache',
        'expiration_time' => DateInterval::createFromDateString('24 hours'),
        'store' => 'file'
    ],

    'tables' => [
        'users' => 'users',
        'roles' => 'roles',
        'abilities' => 'abilities',
        'roles_abilities' => 'roles_abilities',
        'permission_types' => 'permission_types',
        'permissions' => 'permissions'
    ],

    'models' => [
        'ability' => \Laranoia\Permissions\Models\Ability::class,
        'permission' => \Laranoia\Permissions\Models\Permission::class,
        'role' => \Laranoia\Permissions\Models\Role::class,
        'role_ability' => \Laranoia\Permissions\Models\RoleAbility::class
    ],

];
