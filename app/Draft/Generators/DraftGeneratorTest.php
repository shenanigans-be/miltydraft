<?php

namespace App\Draft\Generators;

use App\Testing\Factories\DraftSettingsFactory;
use App\Testing\TestCase;
use App\TwilightImperium\AllianceTeamMode;
use PHPUnit\Framework\Attributes\Test;

class DraftGeneratorTest extends TestCase
{
    #[Test]
    public function itCanGenerateADraftBasedOnSettings()
    {
        // don't have to check slices and factions, that's tested in their respective generators
        $settings = DraftSettingsFactory::make([
            'numberOfPlayers' => 4,
        ]);
        $generator = new DraftGenerator($settings);

        $draft = $generator->generate();

        $this->assertNotEmpty($draft->slicePool);
        $this->assertNotEmpty($draft->factionPool);
        $this->assertNotNull($draft->currentPlayerId);

        unset($generator);
    }

    #[Test]
    public function itCanGeneratePlayerData()
    {
        $originalPlayerNames = ['Alice', 'Bob', 'Christine', 'David'];
        $settings = DraftSettingsFactory::make([
            'playerNames' => $originalPlayerNames,
        ]);
        $generator = new DraftGenerator($settings);

        $draft = $generator->generate();

        $playerIds = [];
        $playerNames = [];
        foreach($draft->players as $player) {
            $playerIds[] = $player->id->value;
            $playerNames[] = $player->name;
            $this->assertFalse($player->claimed);
            $this->assertNull($player->pickedFaction);
            $this->assertNull($player->pickedSlice);
            $this->assertNull($player->pickedPosition);
        }

        $this->assertCount(count($originalPlayerNames), array_unique($playerIds));
        $this->assertCount(count($originalPlayerNames), array_unique($playerNames));

        unset($generator);
    }

    #[Test]
    public function itCanGeneratePlayerDataInPresetOrder()
    {
        $originalPlayerNames = ['Alice', 'Bob', 'Christine', 'David'];
        $settings = DraftSettingsFactory::make([
            'playerNames' => $originalPlayerNames,
            'presetDraftOrder' => true
        ]);
        $generator = new DraftGenerator($settings);

        $draft = $generator->generate();

        $playerNames = [];
        foreach($draft->players as $player) {
            $playerNames[] = $player->name;
        }

        $this->assertSame($originalPlayerNames, $playerNames);
        unset($generator);
    }

    #[Test]
    public function itCanGeneratePlayerDataForAlliances()
    {
        $originalPlayerNames = ['Alice', 'Bob', 'Christine', 'David', 'Ellis', ''];
        $settings = DraftSettingsFactory::make([
            'playerNames' => $originalPlayerNames,
            'allianceMode' => true,
            'allianceTeamMode' => AllianceTeamMode::PRESET,
        ]);



    }

}