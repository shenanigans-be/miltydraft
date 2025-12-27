<?php

declare(strict_types=1);

namespace App\Draft\Commands;

use App\Draft\Pick;
use App\Draft\PickCategory;
use App\Draft\PlayerId;
use App\Shared\Command;
use App\Testing\TestCase;
use App\Testing\UsesTestDraft;
use PHPUnit\Framework\Attributes\Test;

class UndoLastPickTest extends TestCase
{
    use UsesTestDraft;

    #[Test]
    public function itImplementsCommand(): void
    {
        $cmd = new UndoLastPick($this->testDraft);
        $this->assertInstanceOf(Command::class, $cmd);
    }

    protected function makeTwoPicks(): void
    {
        $player1Id = PlayerId::fromString(array_keys($this->testDraft->players)[0]);
        $player2Id = PlayerId::fromString(array_keys($this->testDraft->players)[1]);

        (new PlayerPick(
            $this->testDraft,
            new Pick($player1Id, PickCategory::SLICE, '4'),
        )
        )->handle();
        (new PlayerPick(
            $this->testDraft,
            new Pick($player2Id, PickCategory::SLICE, '3'),
        )
        )->handle();
    }

    #[Test]
    public function itCanUndoLastPick(): void
    {
        $player2Id = PlayerId::fromString(array_keys($this->testDraft->players)[1]);
        $this->makeTwoPicks();
        $cmd = new UndoLastPick($this->testDraft);

        $cmd->handle();
        $this->reloadDraft();

        $this->assertNull($this->testDraft->playerById($player2Id)->pickedSlice);
    }

    #[Test]
    public function itThrowsAnErrorWhenNothingHasBeenPicked(): void
    {
        $cmd = new UndoLastPick($this->testDraft);

        $this->expectException(\Exception::class);

        $cmd->handle();
    }

    #[Test]
    public function itUpdatesCurrentPlayer(): void
    {
        $player2Id = PlayerId::fromString(array_keys($this->testDraft->players)[1]);
        $this->makeTwoPicks();
        $cmd = new UndoLastPick($this->testDraft);

        $cmd->handle();
        $this->reloadDraft();

        $this->assertSame($this->testDraft->currentPlayerId->value, $player2Id->value);
    }

    #[Test]
    public function itRemovesPickFromLog(): void
    {
        $player1Id = PlayerId::fromString(array_keys($this->testDraft->players)[0]);
        $this->makeTwoPicks();
        $cmd = new UndoLastPick($this->testDraft);

        $cmd->handle();
        $this->reloadDraft();

        $this->assertCount(1, $this->testDraft->log);
        $this->assertSame($this->testDraft->log[0]->playerId->value, $player1Id->value);
    }

    #[Test]
    public function itSetsTheDraftToUndoneIfUndoingLastPick(): void
    {
        $order = array_merge(
            array_keys($this->testDraft->players),
            array_keys(array_reverse($this->testDraft->players)),
            array_keys($this->testDraft->players),
        );

        foreach($order as $currentPlayer) {
            $pick = new Pick($this->testDraft->currentPlayerId, PickCategory::FACTION, 'foo');
            $this->testDraft->log[] = $pick;
            $this->testDraft->updateCurrentPlayer();
        }

        $this->assertTrue($this->testDraft->isDone);
        $this->assertNull($this->testDraft->currentPlayerId);

        // update player pick so the undo command doesn't complain
        $lastPlayer = PlayerId::fromString(array_key_last($this->testDraft->players));
        $this->testDraft->updatePlayerData($this->testDraft->playerById($lastPlayer)->pick(
            new Pick(
                $lastPlayer,
                PickCategory::FACTION,
                'foo',
            ),
        ));

        $cmd = new UndoLastPick($this->testDraft);
        $cmd->handle();
        $this->reloadDraft();

        $this->assertFalse($this->testDraft->isDone);
        $this->assertNotNull($this->testDraft->currentPlayerId);
    }
}