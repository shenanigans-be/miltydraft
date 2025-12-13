<?php

namespace App\Testing;

use App\TwilightImperium\Planet;
use App\TwilightImperium\Tile;
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
    public static function make(array $planets = [], array $wormholes = [], ?string $anomaly = null): Tile
    {
        return new Tile(
            "tile",
            TileType::BLUE,
            $planets,
            [],
            $wormholes,
            $anomaly
        );
    }
}