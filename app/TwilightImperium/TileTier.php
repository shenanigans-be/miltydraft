<?php

namespace App\TwilightImperium;

/**
 * This provides a way to differentiate mediocre or bad tiles from good ones.
 * In our slice drafting, we don't make completely random slices, we put our thumb on the scale a bit
 * to make every slice as competitive as possible.
 *
 * Obviously this categorisation is more art than science, so tiers might shift over time.
 *
 * I also keep having this idea: https://github.com/shenanigans-be/miltydraft/issues/11 but that would take a lot of time I don't have
 */
enum TileTier: string
{
    case HIGH = "high";
    case MEDIUM = "mid";
    case LOW = "low";
    case RED = "red";
    case NONE = "none";
}