<?php

namespace App\Testing\Factories;

use App\TwilightImperium\Edition;
use App\TwilightImperium\Planet;
use App\TwilightImperium\Tile;
use App\TwilightImperium\TileTier;
use App\TwilightImperium\TileType;
use App\TwilightImperium\Wormhole;

class TileFactory
{
    /**
     * @param array<Planet> $planets
     * @param array<Wormhole> $wormholes
     * @param string|null $anomaly
     * @return Tile
     */
    public static function make(
        array $planets = [],
        array $wormholes = [],
        ?string $anomaly = null,
        TileTier $tier = TileTier::MEDIUM,
        Edition $edition = Edition::BASE_GAME
    ): Tile {
        return new Tile(
            "tile-" . bin2hex(random_bytes(2)),
            TileType::BLUE,
            $tier,
            $edition,
            $planets,
            [],
            $wormholes,
            $anomaly
        );
    }
}