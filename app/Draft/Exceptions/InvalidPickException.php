<?php

declare(strict_types=1);

namespace App\Draft\Exceptions;

use App\Draft\PickCategory;

class InvalidPickException extends \Exception
{
    public static function playerHasAlreadyPicked(PickCategory $category)
    {
        return new self('Player has already picked ' . $category->value);
    }

    public static function optionAlreadyPicked($value)
    {
        return new self('Other player has already picked ' . $value);
    }

    public static function cannotUnpick(PickCategory $category)
    {
        return new self('Cannot undo pick: Player has not picked ' . $category->value);
    }

    public static function notPlayersTurn()
    {
        return new self("It's not your turn!");
    }
}