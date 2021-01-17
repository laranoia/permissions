<?php

namespace Laranoia\Permissions\Exceptions;

class AbilityDoesNotExist extends \InvalidArgumentException
{
    public static function named($name)
    {
        return new static('The ability "'.$name.'" does not exist');
    }
}