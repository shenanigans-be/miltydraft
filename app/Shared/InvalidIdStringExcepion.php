<?php

declare(strict_types=1);

namespace App\Shared;

class InvalidIdStringExcepion extends \Exception
{
    public static function emptyId($class)
    {
        return new self('String id ' . $class . ' cannot be empty');
    }
}