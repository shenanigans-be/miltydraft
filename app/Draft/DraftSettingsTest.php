<?php

namespace App\Draft;

use App\Testing\DraftSettingsFactory;
use App\Testing\TestCase;
use App\Testing\TestDrafts;
use App\TwilightImperium\AllianceTeamMode;
use App\TwilightImperium\AllianceTeamPosition;
use App\TwilightImperium\Edition;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

class DraftSettingsTest extends TestCase
{
    #[Test]
    public function itCanBeConvertedToAnArray()
    {
        $draftSettings = new DraftSettings(
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

    #[DataProviderExternal(TestDrafts::class, "provideTestDrafts")]
    #[Test]
    public function itCanBeInstantiatedFromJson($data) {
        $draftSettings = DraftSettings::fromJson($data['config']);

        $this->assertSame($data['config']['name'], (string) $draftSettings->name);
        $this->assertSame($data['config']['num_slices'], $draftSettings->numberOfSlices);
        $this->assertSame($data['config']['num_factions'], $draftSettings->numberOfFactions);
        $this->assertSame($data['config']['include_pok'], $draftSettings->includesTileSet(Edition::PROPHECY_OF_KINGS));
        $this->assertSame($data['config']['include_ds_tiles'], $draftSettings->includesTileSet(Edition::DISCORDANT_STARS_PLUS));
        $this->assertSame($data['config']['include_te_tiles'], $draftSettings->includesTileSet(Edition::THUNDERS_EDGE));
        $this->assertSame($data['config']['include_base_factions'], $draftSettings->includesFactionSet(Edition::BASE_GAME));
        $this->assertSame($data['config']['include_pok_factions'], $draftSettings->includesFactionSet(Edition::PROPHECY_OF_KINGS));
        $this->assertSame($data['config']['include_te_factions'], $draftSettings->includesFactionSet(Edition::THUNDERS_EDGE));
        $this->assertSame($data['config']['include_discordant'], $draftSettings->includesFactionSet(Edition::DISCORDANT_STARS));
        $this->assertSame($data['config']['include_discordantexp'], $draftSettings->includesFactionSet(Edition::DISCORDANT_STARS_PLUS));
        $this->assertSame($data['config']['include_keleres'], $draftSettings->includeCouncilKeleresFaction);
        $this->assertSame($data['config']['preset_draft_order'], $draftSettings->presetDraftOrder);
        $this->assertSame($data['config']['min_wormholes'], $draftSettings->minimumWormholes);
        $this->assertSame($data['config']['min_legendaries'], $draftSettings->minimumLegendaryPlanets);
        $this->assertSame($data['config']['max_1_wormhole'], $draftSettings->maxOneWormholesPerSlice);
        $this->assertSame((float) $data['config']['minimum_optimal_influence'], $draftSettings->minimumOptimalInfluence);
        $this->assertSame((float) $data['config']['minimum_optimal_resources'], $draftSettings->minimumOptimalResources);
        $this->assertSame((float) $data['config']['minimum_optimal_total'], $draftSettings->minimumOptimalTotal);
        $this->assertSame((float) $data['config']['maximum_optimal_total'], $draftSettings->maximumOptimalTotal);
        if ($data['config']['custom_factions'] == null) {
            $this->assertSame([], $draftSettings->customFactions);
        } else {
            $this->assertSame($data['config']['custom_factions'], $draftSettings->customFactions);
        }
        if ($data['config']['custom_slices'] == null) {
            $this->assertSame([], $draftSettings->customSlices);
        } else {
            $this->assertSame($data['config']['custom_slices'], $draftSettings->customSlices);
        }
        $this->assertSame($data['config']['seed'], $draftSettings->seed->getValue());
        if ($data['config']['alliance'] != null) {
            $this->assertSame($data['config']['alliance']['alliance_teams'], $draftSettings->allianceTeamMode->value);
            $this->assertSame($data['config']['alliance']['alliance_teams_position'], $draftSettings->allianceTeamPosition->value);
            $this->assertSame($data['config']['alliance']['force_double_picks'], $draftSettings->allianceForceDoublePicks);
        } else {
            $this->assertNull($draftSettings->allianceTeamMode);
            $this->assertNull($draftSettings->allianceTeamPosition);
            $this->assertNull($draftSettings->allianceForceDoublePicks);
        }
    }
}