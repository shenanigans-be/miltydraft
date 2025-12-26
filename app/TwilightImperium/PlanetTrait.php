<?php

declare(strict_types=1);

namespace App\TwilightImperium;

enum PlanetTrait:string
{
    case CULTURAL = 'cultural';
    case HAZARDOUS = 'hazardous';
    case INDUSTRIAL = 'industrial';
}