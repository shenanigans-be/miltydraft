<?php

namespace App\Draft;

use App\Shared\IdStringBehavior;

class PlayerId implements \Stringable
{
    use IdStringBehavior;

    public static function generate()
    {
        return self::fromString('p_' .  bin2hex(random_bytes(8)));
    }
}