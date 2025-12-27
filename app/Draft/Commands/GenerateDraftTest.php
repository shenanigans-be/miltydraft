<?php

declare(strict_types=1);

namespace App\Draft\Commands;

use App\Draft\Player;
use App\Shared\Command;
use App\Testing\Factories\DraftSettingsFactory;
use App\Testing\TestCase;
use App\TwilightImperium\AllianceTeamMode;
use PHPUnit\Framework\Attributes\Test;

class GenerateDraftTest extends TestCase
{
    #[Test]
    public function itImplementsCommand(): void
    {
        $cmd = new GenerateDraft(DraftSettingsFactory::make());
        $this->assertInstanceOf(Command::class, $cmd);
    }

    #[Test]
    public function itCanGenerateADraftBasedOnSettings(): void
    {
        // don't have to check slices and factions, that's tested in their respective generators
        $settings = DraftSettingsFactory::make([
            'numberOfPlayers' => 4,
        ]);
        $generator = new GenerateDraft($settings);
        $draft = $generator->handle();

        $this->assertNotEmpty($draft->slicePool);
        $this->assertNotEmpty($draft->factionPool);
        $this->assertEquals($draft->currentPlayerId, array_values($draft->players)[0]->id);
    }

    #[Test]
    public function itCanGeneratePlayerData(): void
    {
        $originalPlayerNames = ['Alice', 'Bob', 'Christine', 'David'];
        $settings = DraftSettingsFactory::make([
            'playerNames' => $originalPlayerNames,
        ]);
        $generator = new GenerateDraft($settings);
        $draft = $generator->handle();

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
    }

    #[Test]
    public function itCanGeneratePlayerDataInPresetOrder(): void
    {
        $originalPlayerNames = ['Alice', 'Bob', 'Christine', 'David'];
        $settings = DraftSettingsFactory::make([
            'playerNames' => $originalPlayerNames,
            'presetDraftOrder' => true,
        ]);
        $generator = new GenerateDraft($settings);

        $draft = $generator->handle();

        $playerNames = [];
        foreach($draft->players as $player) {
            $playerNames[] = $player->name;
        }

        $this->assertSame($originalPlayerNames, $playerNames);
        unset($generator);
    }

    #[Test]
    public function itCanGeneratePlayerDataForAlliances(): void
    {
        $originalPlayerNames = ['Alice', 'Bob', 'Christine', 'David', 'Elliot', 'Frank'];
        $settings = DraftSettingsFactory::make([
            'playerNames' => $originalPlayerNames,
            'allianceMode' => true,
            'allianceTeamMode' => AllianceTeamMode::PRESET,
            'presetDraftOrder' => true,
        ]);
        $generator = new GenerateDraft($settings);
        $draft = $generator->handle();

        /**
         * @var array<Player> $players
         */
        $players = array_values($draft->players);

        $this->assertSame('Alice', $players[0]->name);
        $this->assertSame('A', $players[0]->team);
        $this->assertSame('Bob', $players[1]->name);
        $this->assertSame('A', $players[1]->team);
        $this->assertSame('Christine', $players[2]->name);
        $this->assertSame('B', $players[2]->team);
        $this->assertSame('David', $players[3]->name);
        $this->assertSame('B', $players[3]->team);
        $this->assertSame('Elliot', $players[4]->name);
        $this->assertSame('C', $players[4]->team);
        $this->assertSame('Frank', $players[5]->name);
        $this->assertSame('C', $players[5]->team);
    }
}