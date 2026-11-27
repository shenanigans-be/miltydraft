<?php

declare(strict_types=1);

namespace App\Draft\Commands;

use App\Draft\Pick;
use App\Draft\PickCategory;
use App\Draft\PlayerId;
use App\Draft\Slice;
use App\Shared\Command;
use App\Testing\Factories\DraftSettingsFactory;
use App\Testing\TestCase;
use App\Testing\UsesTestDraft;
use App\TwilightImperium\Faction;
use PHPUnit\Framework\Attributes\Test;

class UpdateDraftSettingsTest extends TestCase
{
    use UsesTestDraft;

    #[Test]
    public function itImplementsCommand(): void
    {
        $cmd = new UpdateDraftSettings($this->testDraft, $this->testDraft->settings, []);
        $this->assertInstanceOf(Command::class, $cmd);
    }

    #[Test]
    public function itRenamesPlayersAndUpdatesSettingsWithoutRegenerating(): void
    {
        $oldSlices = array_map(fn (Slice $slice) => $slice->tileIds(), $this->testDraft->slicePool);
        $oldFactions = array_map(fn (Faction $faction) => $faction->name, $this->testDraft->factionPool);

        $newNames = [];
        foreach ($this->testDraft->players as $id => $player) {
            $newNames[$id] = 'Renamed ' . $player->name;
        }

        $settings = DraftSettingsFactory::make([
            'playerNames' => array_values($newNames),
            'name' => (string) $this->testDraft->settings->name,
            'seed' => $this->testDraft->settings->seed->getValue(),
            'numberOfSlices' => 8,
            'numberOfFactions' => 9,
            'tileSets' => $this->testDraft->settings->tileSets,
            'factionSets' => $this->testDraft->settings->factionSets,
        ]);

        $cmd = new UpdateDraftSettings($this->testDraft, $settings, $newNames);
        $cmd->handle();

        $this->reloadDraft();

        foreach ($this->testDraft->players as $id => $player) {
            $this->assertSame($newNames[$id], $player->name);
        }
        $this->assertSame(array_values($newNames), $this->testDraft->settings->playerNames);
        $this->assertSame(8, $this->testDraft->settings->numberOfSlices);
        $this->assertSame(9, $this->testDraft->settings->numberOfFactions);

        $newSlices = array_map(fn (Slice $slice) => $slice->tileIds(), $this->testDraft->slicePool);
        $newFactions = array_map(fn (Faction $faction) => $faction->name, $this->testDraft->factionPool);
        $this->assertSame($oldSlices, $newSlices);
        $this->assertSame($oldFactions, $newFactions);
    }

    #[Test]
    public function itCannotChangeSettingsOnceTheDraftHasStarted(): void
    {
        $this->testDraft->log[] = new Pick(
            PlayerId::fromString(array_key_first($this->testDraft->players)),
            PickCategory::FACTION,
            'The Mentak Coalition',
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot change settings once the draft has started');

        $cmd = new UpdateDraftSettings($this->testDraft, $this->testDraft->settings, []);
        $cmd->handle();
    }
}
