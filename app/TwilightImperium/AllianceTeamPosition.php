<?php

declare(strict_types=1);

namespace App\TwilightImperium;

enum AllianceTeamPosition: string
{
    case NONE = 'none';
    case NEIGHBORS = 'neighbors';
    case OPPOSITES = 'opposites';
}