<?php

namespace Laranoia\Permissions\Exceptions;

class RoleDoesNotExist extends \InvalidArgumentException
{
    public static function named($name)
    {
        return new static('The role "'.$name.'" does not exist');
    }
}