<?php

declare(strict_types=1);

namespace App\Draft;

enum PickCategory: string {
    case FACTION = 'faction';
    case SLICE = 'slice';
    case POSITION = 'position';
}