<?php

namespace App\Draft;

use App\Testing\DraftSettingsFactory;
use App\Testing\TestCase;
use App\TwilightImperium\AllianceTeamMode;
use App\TwilightImperium\AllianceTeamPosition;
use App\TwilightImperium\Edition;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class DraftSettingsTest extends TestCase
{
    #[Test]
    public function itCanBeConvertedToAnArray()
    {
        $draftSettings = new DraftSettings(
            4,
            ["john", "mike", "suzy", "robin"],
            true,
            new DraftName("Testgame"),
            new DraftSeed(123),
            5,
            8,
            [
                Edition::BASE_GAME,
                Edition::PROPHECY_OF_KINGS,
                Edition::THUNDERS_EDGE,
                Edition::DISCORDANT_STARS_PLUS,
            ],
            [
                Edition::PROPHECY_OF_KINGS,
                Edition::THUNDERS_EDGE,
                Edition::DISCORDANT_STARS_PLUS,
            ],
            true,
            2,
            true,
            3,
            4.5,
            7.2,
            18.3,
            29,
            [
                "The Titans of Ul",
                "Free Systems Compact"
            ],
            [
                [
                    1, 2, 3, 4, 5
                ]
            ],
            true,
            AllianceTeamMode::RANDOM,
            AllianceTeamPosition::NEIGHBORS,
            true
        );

        $array = $draftSettings->toArray();

        $this->assertSame(4, $array['num_players']);
        $this->assertSame(["john", "mike", "suzy", "robin"], $array['players']);
        $this->assertSame("Testgame", $array['name']);
        $this->assertSame(5, $array['num_slices']);
        $this->assertSame(8, $array['num_factions']);
        $this->assertSame(true, $array['include_pok']);
        $this->assertSame(true, $array['include_ds_tiles']);
        $this->assertSame(true, $array['include_te_tiles']);
        $this->assertSame(false, $array['include_base_factions']);
        $this->assertSame(true, $array['include_pok_factions']);
        $this->assertSame(true, $array['include_keleres']);
        $this->assertSame(false, $array['include_discordant']);
        $this->assertSame(true, $array['include_discordantexp']);
        $this->assertSame(true, $array['include_te_factions']);
        $this->assertSame(true, $array['preset_draft_order']);
        $this->assertSame(2, $array['min_wormholes']);
        $this->assertSame(3, $array['min_legendaries']);
        $this->assertSame(true, $array['max_1_wormhole']);
        $this->assertSame(4.5, $array['minimum_optimal_influence']);
        $this->assertSame(7.2, $array['minimum_optimal_resources']);
        $this->assertSame(18.3, $array['minimum_optimal_total']);
        $this->assertSame(29.0, $array['maximum_optimal_total']);
        $this->assertSame([
            "The Titans of Ul",
            "Free Systems Compact"
        ], $array['custom_factions']);
        $this->assertSame([
            [
                1, 2, 3, 4, 5
            ]
        ], $array['custom_slices']);
        $this->assertSame(123, $array['seed']);
        $this->assertSame("random", $array['alliance']['alliance_teams']);
        $this->assertSame("neighbors", $array['alliance']['alliance_teams_position']);
        $this->assertSame(true, $array['alliance']['force_double_picks']);
    }

    public static function validationCases()
    {
        yield "When player count does not match player names" => [
            'data' => [
                'numberOfPlayers' => 4,
                'players' => [
                    'sam',
                    'josie',
                    'kyle'
                ]
            ],
            'exception' => InvalidDraftSettingsException::playerCountDoesNotMatch()
        ];
        yield "When player names are not unique" => [
            "data" => [
                'players' => [
                    'sam',
                    'sam',
                    'kyle'
                ]
            ],
            'exception' => InvalidDraftSettingsException::playerNamesNotUnique()
        ];
        yield "When not enough players" => [
            "data" => [
                'players' => [
                    'sam',
                    'kyle'
                ]
            ],
            'exception' => InvalidDraftSettingsException::notEnoughPlayers()
        ];
        yield "When checking slice count" => [
            "data" => [
                'numberOfPlayers' => 4,
                'numberOfSlices' => 3
            ],
            'exception' => InvalidDraftSettingsException::notEnoughSlicesForPlayers()
        ];
    }

    #[DataProvider("validationCases")]
    #[Test]
    public function itThrowsValidationErrors($data, \Exception $exception)
    {
        $draft = DraftSettingsFactory::make($data);

        $this->expectException(InvalidDraftSettingsException::class);
        $this->expectExceptionMessage($exception->getMessage());
        $draft->validate();
    }

    #[Test]
    public function itValidatesFactionCount()
    {
        $draft = DraftSettingsFactory::make([
            'numberOfPlayers' => 4,
            'numberOfFactions' => 2
        ]);

        $this->expectException(InvalidDraftSettingsException::class);
        $this->expectExceptionMessage(InvalidDraftSettingsException::notEnoughFactionsForPlayers()->getMessage());
        $draft->validate();
    }

    #[Test]
    public function itValidatesNumberOfSlices() {
        $draft = DraftSettingsFactory::make([
            'numberOfSlices' => 7,
            'tileSets' => [
                Edition::BASE_GAME
            ]
        ]);

        $this->expectException(InvalidDraftSettingsException::class);
        $this->expectExceptionMessage(InvalidDraftSettingsException::notEnoughTilesForSlices(6)->getMessage());
        $draft->validate();
    }

    #[Test]
    public function itValidatesOptimalMaximum() {
        $draft = DraftSettingsFactory::make([
            'minimumOptimalTotal' => 7,
            'maximumOptimalTotal' => 4,
        ]);

        $this->expectException(InvalidDraftSettingsException::class);
        $this->expectExceptionMessage(InvalidDraftSettingsException::invalidMaximumOptimal()->getMessage());
        $draft->validate();
    }

    #[Test]
    public function itValidatesMinimumLegendaryPlanets() {
        $draft = DraftSettingsFactory::make([
            'minimumLegendaryPlanets' => 6,
            'tileSets' => [
                Edition::BASE_GAME,
                Edition::THUNDERS_EDGE
            ]
        ]);

        $this->expectException(InvalidDraftSettingsException::class);
        $this->expectExceptionMessage(InvalidDraftSettingsException::notEnoughLegendaryPlanets(5)->getMessage());
        $draft->validate();
    }

    #[Test]
    public function itValidatesMinimumLegendaryPlanetsAgainstSlices() {
        $draft = DraftSettingsFactory::make([
            'numberOfPlayers' => 5,
            'minimumLegendaryPlanets' => 6,
            'numberOfSlices' => 5
        ]);

        $this->expectException(InvalidDraftSettingsException::class);
        $this->expectExceptionMessage(InvalidDraftSettingsException::notEnoughSlicesForLegendaryPlanets()->getMessage());
        $draft->validate();
    }

    public static function seedValues(): iterable
    {
        yield "When seed is negative" => [
            "seed" => -1,
            "valid" => false
        ];
        yield "When seed is too high" => [
            "seed" => DraftSeed::MAX_VALUE + 12,
            "valid" => false
        ];
        yield "When seed is valid" => [
            "seed" => 50312,
            "valid" => true
        ];
    }

    #[DataProvider("seedValues")]
    #[Test]
    public function itValidatesSeed($seed, $valid) {
        $draft = DraftSettingsFactory::make([
            'seed' => $seed
        ]);

        if ($valid) {
            $this->assertTrue($draft->validate());
        } else {
            $this->expectException(InvalidDraftSettingsException::class);
            $this->expectExceptionMessage(InvalidDraftSettingsException::invalidSeed()->getMessage());

            $draft->validate();
        }
    }

    #[Test]
    public function itValidatesFactionSetCount() {
        $draft = DraftSettingsFactory::make([
            'numberOfFactions' => 20,
            'factionSets' => [
                Edition::BASE_GAME
            ]
        ]);

        $this->expectException(InvalidDraftSettingsException::class);
        $this->expectExceptionMessage(InvalidDraftSettingsException::notEnoughFactionsInSet(17)->getMessage());

        $draft->validate();
    }

    #[Test]
    public function itValidatesCustomSlices() {
        $draft = DraftSettingsFactory::make([
            'numberOfPlayers' => 5,
            'customSlices' => [
                [1, 2, 3, 4, 5]
            ]
        ]);

        $this->expectException(InvalidDraftSettingsException::class);
        $this->expectExceptionMessage(InvalidDraftSettingsException::notEnoughCustomSlices()->getMessage());

        $draft->validate();
    }
}