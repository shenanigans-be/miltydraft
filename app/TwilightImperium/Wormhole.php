<?php

namespace App\TwilightImperium;

enum Wormhole: string
{
    case ALPHA = "alpha";
    case BETA = "beta";
    case GAMMA = "gamma";
    case DELTA = "delta";
    case EPSILON = "epsilon";


    /**
     * @todo refactor tiles.json to use arrays instead of a single string
     *
     * @param string $wormhole
     * @return array<Wormhole>
     */
    public static function fromJsonData(?string $wormhole): array
    {
        if ($wormhole == null) return [];

        if ($wormhole == "alpha-beta") {
            return [
                self::ALPHA,
                self::BETA
            ];
        } else {
            return [
                self::from($wormhole)
            ];
        }
    }
}