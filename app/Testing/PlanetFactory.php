<?php

namespace App\Testing;

use App\TwilightImperium\Planet;
use App\TwilightImperium\PlanetTrait;
use App\TwilightImperium\TechSpecialties;
use App\TwilightImperium\Tile;
use App\TwilightImperium\TileType;
use App\TwilightImperium\Wormhole;
use Faker\Factory;

class PlanetFactory
{
    public static function make(array $properties = []): Planet
    {
        $faker = Factory::create();
        return new Planet(
            $properties['name'] ?? $faker->word(),
            $properties['resources'] ?? $faker->numberBetween(0, 4),
            $properties['influence'] ?? $faker->numberBetween(0, 4),
            $properties['legendary'] ?? null,
            $properties['traits'] ?? $faker->randomElements([
                PlanetTrait::INDUSTRIAL,
                PlanetTrait::HAZARDOUS,
                PlanetTrait::CULTURAL,
            ], 1),
            $properties['specialties'] ??$faker->randomElements([
                TechSpecialties::WARFARE,
                TechSpecialties::PROPULSION,
                TechSpecialties::CYBERNETIC,
                TechSpecialties::BIOTIC,
            ], $faker->numberBetween(0, 2)),
        );
    }
}