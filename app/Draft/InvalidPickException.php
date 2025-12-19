<?php

namespace App\Draft;

class InvalidPickException extends \Exception
{
    public static function playerHasAlreadyPicked(PickCategory $category)
    {
        return new self("Player has already picked " . $category);
    }
}