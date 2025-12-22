<?php

namespace App\Draft\Exceptions;

class InvalidSliceException extends \Exception
{
    public static function notEnoughTiles(): self
    {
        return new self("Not enough tiles");
    }

    public static function doesNotMeetRequirements($reason): self
    {
        return new self("Slice validation fails on: " . $reason);
    }

    public static function hasNoValidArrangement(): self
    {
        return new self("Slice has no valid arrangement");
    }
}