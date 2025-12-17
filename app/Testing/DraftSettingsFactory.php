<?php

namespace App\Testing;

use App\Draft\DraftName;
use App\Draft\DraftSeed;
use App\Draft\DraftSettings;
use App\TwilightImperium\AllianceTeamMode;
use App\TwilightImperium\AllianceTeamPosition;
use App\TwilightImperium\Edition;
use Faker\Factory;

class DraftSettingsFactory
{
    public static function make(array $properties = []): DraftSettings
    {
        $faker = Factory::create();


        if (isset($properties['numberOfPlayers'])) {
            $numberOfPlayers = $properties['numberOfPlayers'];
        } elseif (isset($properties['playerNames'])) {
            $numberOfPlayers = count($properties['playerNames']);
        } else {
            $numberOfPlayers = 6;
        }

        $names = $properties['playerNames'] ?? array_map(fn () => $faker->name(), range(1, $numberOfPlayers));

        $allianceMode = $properties['allianceMode'] ?? false;

        return new DraftSettings(
            $names,
            $properties['presetDraftOrder'] ?? $faker->boolean(),
            new DraftName($properties['name'] ?? null),
            new DraftSeed($properties['seed'] ?? null),
            $properties['numberOfSlices'] ?? $numberOfPlayers + 2,
            $properties['numberOfFactions'] ?? $numberOfPlayers + 2,
            $properties['tileSets'] ?? [
                Edition::BASE_GAME,
                Edition::PROPHECY_OF_KINGS,
                Edition::THUNDERS_EDGE
            ],
            $properties['factionSets'] ?? [
                Edition::BASE_GAME,
                Edition::PROPHECY_OF_KINGS,
                Edition::THUNDERS_EDGE
            ],
            $properties['includeCouncilKeleresFaction'] ?? false,
            $properties['minimumWormholes'] ?? 0,

            $properties['maxOneWormholePerSlice'] ?? $faker->boolean(),
            $properties['minimumLegendaryPlanets'] ?? $faker->numberBetween(0, 1),
            $properties['minimumOptimalInfluence'] ?? 4,
            $properties['minimumOptimalResources'] ?? 2.5,
            $properties['minimumOptimalTotal'] ?? 9,
            $properties['maximumOptimalTotal'] ?? 13,
            $properties['customFactions'] ?? [],
            $properties['customSlices'] ?? [],
            $allianceMode,
            $allianceMode ? $properties['allianceTeamMode'] ?? AllianceTeamMode::RANDOM : null,
            $allianceMode ? $properties['allianceTeamPosition'] ?? AllianceTeamPosition::OPPOSITES : null,
            $allianceMode ? $properties['allianceForceDoublePicks'] ?? false : null,
        );
    }
}