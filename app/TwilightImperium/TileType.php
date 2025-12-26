<?php

declare(strict_types=1);

namespace App\TwilightImperium;

enum TileType: string {
    case RED = 'red';
    case GREEN = 'green';
    case BLUE = 'blue';
    case HYPERLANE = 'hyperlane';
}