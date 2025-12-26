<?php

namespace App\Http\RequestHandlers;

use App\Draft\Commands\GenerateDraft;
use App\Draft\Exceptions\InvalidDraftSettingsException;
use App\Http\HttpRequest;
use App\Testing\FakesCommands;
use App\Testing\RequestHandlerTestCase;
use App\Testing\UsesTestDraft;
use App\TwilightImperium\AllianceTeamMode;
use App\TwilightImperium\AllianceTeamPosition;
use App\TwilightImperium\Edition;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class HandleGenerateDraftRequestTest extends RequestHandlerTestCase
{
    use FakesCommands;
    use UsesTestDraft;

    protected string $requestHandlerClass = HandleGenerateDraftRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler()
    {
        $this->assertIsConfiguredAsHandlerForRoute('/api/generate');
    }

    #[Test]
    public function itReturnsErrorWhenSettingsAreInvalid()
    {
        $response = $this->handleRequest([], [
            'seed' => -1
        ]);

        $this->assertSame($response->code, 400);
        $this->assertJsonResponseSame(['error' => InvalidDraftSettingsException::invalidSeed()->getMessage()], $response);
    }

    public static function settingsPayload()
    {
        yield 'Player Names' => [
            'postData' => [
                'num_players' => 4,
                'player' => [
                    'John', 'Paul', 'George', 'Ringo'
                ]
            ],
            'field' => 'playerNames',
            'expected' => ['John', 'Paul', 'George', 'Ringo'],
            'expectedWhenNotSet' => []
        ];
        yield 'Player Names containing empties' => [
            'postData' => [
                'num_players' => 6,
                'player' => [
                    'John', 'Paul', 'George', 'Ringo', '', ''
                ]
            ],
            'field' => 'playerNames',
            'expected' => ['John', 'Paul', 'George', 'Ringo', '', ''],
            'expectedWhenNotSet' => []
        ];
        yield 'Alliance Mode' => [
            'postData' => [
                'alliance_on' => true,
                'alliance_teams' => AllianceTeamMode::RANDOM->value,
                'alliance_teams_position' => AllianceTeamPosition::NONE->value,
            ],
            'field' => 'allianceMode',
            'expected' => true,
            'expectedWhenNotSet' => false,
        ];
        yield 'Custom Slices' => [
            'postData' => [
                'custom_slices' => "1,2,3,4,5\n6,7,8,9,10\n11,12,13,14,15"
            ],
            'field' => 'customSlices',
            'expected' => [
               ['1', '2', '3', '4', '5'],
               ['6', '7', '8', '9', '10'],
               ['11', '12', '13', '14', '15'],
            ],
            'expectedWhenNotSet' => [],
        ];
        yield 'Preset Draft Order' => [
            'postData' => [
                'preset_draft_order' => 'on'
            ],
            'field' => 'presetDraftOrder',
            'expected' => true,
            'expectedWhenNotSet' => false
        ];
        yield 'Number of slices' => [
            'postData' => [
                'num_slices' => '8'
            ],
            'field' => 'numberOfSlices',
            'expected' => 8,
            'expectedWhenNotSet' => 0
        ];
        yield 'Number of factions' => [
            'postData' => [
                'num_factions' => '7'
            ],
            'field' => 'numberOfFactions',
            'expected' => 7,
            'expectedWhenNotSet' => 0
        ];
        yield 'Tile set POK' => [
            'postData' => [
                'include_pok' => 'on'
            ],
            'field' => 'tileSets',
            'expected' => [Edition::BASE_GAME, Edition::PROPHECY_OF_KINGS],
            'expectedWhenNotSet' => [Edition::BASE_GAME]
        ];
        yield 'Tile set DS' => [
            'postData' => [
                'include_ds_tiles' => 'on'
            ],
            'field' => 'tileSets',
            'expected' => [Edition::BASE_GAME, Edition::DISCORDANT_STARS_PLUS],
            'expectedWhenNotSet' => [Edition::BASE_GAME]
        ];
        yield 'Tile set TE' => [
            'postData' => [
                'include_te_tiles' => 'on'
            ],
            'field' => 'tileSets',
            'expected' => [Edition::BASE_GAME, Edition::THUNDERS_EDGE],
            'expectedWhenNotSet' => [Edition::BASE_GAME]
        ];
        yield 'Faction set basegame' => [
            'postData' => [
                'include_base_factions' => 'on'
            ],
            'field' => 'factionSets',
            'expected' => [Edition::BASE_GAME],
            'expectedWhenNotSet' => []
        ];
        yield 'Faction set pok' => [
            'postData' => [
                'include_pok_factions' => 'on'
            ],
            'field' => 'factionSets',
            'expected' => [Edition::PROPHECY_OF_KINGS],
            'expectedWhenNotSet' => []
        ];
        yield 'Faction set te' => [
            'postData' => [
                'include_te_factions' => 'on'
            ],
            'field' => 'factionSets',
            'expected' => [Edition::THUNDERS_EDGE],
            'expectedWhenNotSet' => []
        ];
        yield 'Faction set ds' => [
            'postData' => [
                'include_discordant' => 'on'
            ],
            'field' => 'factionSets',
            'expected' => [Edition::DISCORDANT_STARS],
            'expectedWhenNotSet' => []
        ];
        yield 'Faction set ds+' => [
            'postData' => [
                'include_discordantexp' => 'on'
            ],
            'field' => 'factionSets',
            'expected' => [Edition::DISCORDANT_STARS_PLUS],
            'expectedWhenNotSet' => []
        ];
        yield 'Council Keleres' => [
            'postData' => [
                'include_keleres' => 'on'
            ],
            'field' => 'includeCouncilKeleresFaction',
            'expected' => true,
            'expectedWhenNotSet' => false
        ];
        yield 'Minimum legendary planets' => [
            'postData' => [
                'min_legendaries' => '1'
            ],
            'field' => 'minimumLegendaryPlanets',
            'expected' => 1,
            'expectedWhenNotSet' => 0
        ];
        yield 'Minimum optimal Influence' => [
            'postData' => [
                'min_inf' => '4.5'
            ],
            'field' => 'minimumOptimalInfluence',
            'expected' => 4.5,
            'expectedWhenNotSet' => 0.0
        ];
        yield 'Minimum optimal Resources' => [
            'postData' => [
                'min_res' => '3'
            ],
            'field' => 'minimumOptimalResources',
            'expected' => 3.0,
            'expectedWhenNotSet' => 0.0
        ];
        yield 'Minimum optimal total' => [
            'postData' => [
                'min_total' => '7.3'
            ],
            'field' => 'minimumOptimalTotal',
            'expected' => 7.3,
            'expectedWhenNotSet' => 0.0
        ];
        yield 'Maximum optimal total' => [
            'postData' => [
                'min_total' => '13'
            ],
            'field' => 'minimumOptimalTotal',
            'expected' => 13.0,
            'expectedWhenNotSet' => 0.0
        ];
        yield 'Custom Factions' => [
            'postData' => [
                'custom_factions' => ['Xxcha', 'Keleres']
            ],
            'field' => 'customFactions',
            'expected' => ['Xxcha', 'Keleres'],
            'expectedWhenNotSet' => []
        ];
        yield 'Alliance Team Mode' => [
            'postData' => [
                'alliance_on' => true,
                'alliance_teams' => 'random',
                'alliance_teams_position' => 'neighbors'
            ],
            'field' => 'allianceTeamMode',
            'expected' => AllianceTeamMode::RANDOM,
            'expectedWhenNotSet' => null
        ];
        yield 'Alliance Team Position' => [
            'postData' => [
                'alliance_on' => true,
                'alliance_teams' => 'random',
                'alliance_teams_position' => 'neighbors'
            ],
            'field' => 'allianceTeamPosition',
            'expected' => AllianceTeamPosition::NEIGHBORS,
            'expectedWhenNotSet' => null
        ];
        yield 'Alliance Force double picks' => [
            'postData' => [
                'alliance_on' => true,
                'force_double_picks' => 'on',
                'alliance_teams' => 'random',
                'alliance_teams_position' => 'neighbors'
            ],
            'field' => 'allianceForceDoublePicks',
            'expected' => true,
            'expectedWhenNotSet' => null
        ];
    }

    #[Test]
    #[DataProvider('settingsPayload')]
    public function itParsesSettingsFromRequest($postData, $field, $expected, $expectedWhenNotSet)
    {
        $handler = new HandleGenerateDraftRequest(new HttpRequest([], $postData, []));

        $this->assertSame($expected, $handler->settingValue($field));
    }


    #[Test]
    #[DataProvider('settingsPayload')]
    public function itParsesSettingsFromRequestWhenNotSet($postData, $field, $expected, $expectedWhenNotSet)
    {
        $handler = new HandleGenerateDraftRequest(new HttpRequest([], [], []));
        $this->assertSame($expectedWhenNotSet, $handler->settingValue($field));
    }


    #[Test]
    public function itGeneratesADraft()
    {
        $this->setExpectedReturnValue($this->testDraft);

        $response = $this->handleRequest([
            'num_players' => 4,
            'player' => ['John', 'Paul', 'George', 'Ringo'],
            'include_pok' => true,
            'num_slices' => 4,
            'num_factions' => 4,
            'include_pok_factions' => true,
            'include_base_factions' => true,
        ]);;

        $this->assertCommandWasDispatched(GenerateDraft::class);

        $this->assertResponseOk($response);
        $this->assertResponseJson($response);
    }
}