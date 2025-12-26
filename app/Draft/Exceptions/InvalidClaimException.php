<?php

namespace App\Draft\Exceptions;

class InvalidClaimException extends \Exception
{
    public static function playerAlreadyClaimed() {
        return new self('Cannot claim player: Player is already claimed');
    }

    public static function playerNotClaimed() {
        return new self('Cannot unclaim player: Player not claimed');
    }
}