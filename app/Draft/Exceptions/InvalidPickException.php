<?php

declare(strict_types=1);

namespace App\Draft\Exceptions;

use App\Draft\PickCategory;

class InvalidPickException extends \Exception
{
    public static function playerHasAlreadyPicked(PickCategory $category)
    {
        return new self('Player has already picked ' . $category);
    }
}