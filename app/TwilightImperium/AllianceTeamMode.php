<?php

declare(strict_types=1);

namespace App\TwilightImperium;

enum AllianceTeamMode: string
{
    case RANDOM = 'random';
    case PRESET = 'preset';
}