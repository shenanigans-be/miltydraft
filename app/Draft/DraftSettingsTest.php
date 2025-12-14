<?php

namespace App\Draft;

use App\Testing\TestCase;
use App\TwilightImperium\AllianceTeamMode;
use App\TwilightImperium\AllianceTeamPosition;
use App\TwilightImperium\Edition;
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
}