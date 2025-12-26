<?php

declare(strict_types=1);

namespace App\Draft;

use App\Shared\IdStringBehavior;

class DraftId
{
    use IdStringBehavior;

    public static function generate()
    {
        return date('Ymd') . '_' . bin2hex(random_bytes(8));
    }
}