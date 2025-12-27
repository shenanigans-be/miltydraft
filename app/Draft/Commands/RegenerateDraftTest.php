<?php

declare(strict_types=1);

namespace App\Draft\Commands;

use App\Draft\Slice;
use App\Shared\Command;
use App\Testing\TestCase;
use App\Testing\UsesTestDraft;
use App\TwilightImperium\Faction;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class RegenerateDraftTest extends TestCase
{
    use UsesTestDraft;

    #[Test]
    public function itImplementsCommand(): void
    {
        $cmd = new RegenerateDraft($this->testDraft, true, true, false);
        $this->assertInstanceOf(Command::class, $cmd);
    }

    public static function options() {
        yield 'When regenerating slices' => [
            'slices' => true,
            'factions' => false,
            'order' => false,
        ];
        yield 'When regenerating factions' => [
            'slices' => false,
            'factions' => true,
            'order' => false,
        ];
        yield 'When regenerating order' => [
            'slices' => false,
            'factions' => false,
            'order' => true,
        ];
        yield 'When regenerating everything' => [
            'slices' => true,
            'factions' => true,
            'order' => true,
        ];
    }

    #[Test]
    #[DataProvider('options')]
    public function itCanRegenerateDraft(bool $slices, bool $factions, bool $order): void
    {
        $oldSlices = array_map(fn (Slice $slice) => $slice->tileIds(), $this->testDraft->slicePool);
        $oldFactions = array_map(fn (Faction $faction) => $faction->name, $this->testDraft->factionPool);
        $oldOrder = array_keys($this->testDraft->players);

        $cmd = new RegenerateDraft($this->testDraft, $slices, $factions, $order);
        $cmd->handle();

        $this->reloadDraft();

        $newSlices = array_map(fn (Slice $slice) => $slice->tileIds(), $this->testDraft->slicePool);
        $newFactions = array_map(fn (Faction $faction) => $faction->name, $this->testDraft->factionPool);
        $newOrder = array_keys($this->testDraft->players);

        if ($slices) {
            $this->assertNotSame($oldSlices, $newSlices);
        } else {
            $this->assertSame($oldSlices, $newSlices);
        }

        if ($factions) {
            $this->assertNotSame($oldFactions, $newFactions);
        } else {
            $this->assertSame($oldFactions, $newFactions);
        }

        if ($order) {
            $this->assertEqualsCanonicalizing($oldOrder, $newOrder);
            $this->assertNotEquals($oldOrder, $newOrder);
        } else {
            $this->assertSame($oldOrder, $newOrder);
        }
    }

    #[Test]
    public function itUpdatesCurrentPlayerWhenRegeneratingPlayerOrder(): void
    {
        $cmd = new RegenerateDraft($this->testDraft, false, false, true);

        $cmd->handle();
        $this->reloadDraft();

        $this->assertSame($this->testDraft->currentPlayerId->value, array_key_first($this->testDraft->players));
    }
}