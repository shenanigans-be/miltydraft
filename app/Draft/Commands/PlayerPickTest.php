<?php

declare(strict_types=1);

namespace App\Draft\Commands;

use App\Draft\Exceptions\InvalidPickException;
use App\Draft\Pick;
use App\Draft\PickCategory;
use App\Draft\PlayerId;
use App\Shared\Command;
use App\Testing\TestCase;
use App\Testing\UsesTestDraft;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class PlayerPickTest extends TestCase
{
   use UsesTestDraft;

    #[Test]
    public function itImplementsCommand(): void
    {
        $playerId = PlayerId::fromString(array_key_first($this->testDraft->players));
        $cmd = new PlayerPick($this->testDraft, new Pick($playerId, PickCategory::SLICE, '1'));
        $this->assertInstanceOf(Command::class, $cmd);
    }

    public static function picks()
    {
        yield 'Picking position' => [
            'category' => PickCategory::POSITION,
            'pick' => '1',
        ];
        yield 'Picking faction' => [
            'category' => PickCategory::FACTION,
            'pick' => 'Mahact',
        ];
        yield 'Picking slice' => [
            'category' => PickCategory::SLICE,
            'pick' => '4',
        ];
    }

    #[Test]
    #[DataProvider('picks')]
    public function itCanPerformPick(PickCategory $category, string $pick): void
    {
        $playerId = $this->testDraft->currentPlayerId;
        $pickCmd = new PlayerPick($this->testDraft, new Pick($playerId, $category, $pick));

        $pickCmd->handle();

        $this->reloadDraft();

        $this->assertSame($pick, $this->testDraft->playerById($playerId)->getPick($category));
    }

    #[Test]
    public function itThrowsAnErrorWhenItIsNotPlayersTurn(): void
    {
        $playerId = PlayerId::fromString(array_keys($this->testDraft->players)[1]);
        $pickCmd = new PlayerPick($this->testDraft, new Pick($playerId, PickCategory::POSITION, '2'));

        $this->expectException(InvalidPickException::class);

        $pickCmd->handle();
    }

    #[Test]
    #[DataProvider('picks')]
    public function itSavesPickInLog(PickCategory $category, string $pick): void
    {
        $playerId = PlayerId::fromString(array_key_first($this->testDraft->players));

        $pickVo = new Pick($playerId, $category, $pick);

        $pickCmd = new PlayerPick($this->testDraft, $pickVo);
        $pickCmd->handle();

        $this->reloadDraft();

        $this->assertSame($pickVo->toArray(), $this->testDraft->log[count($this->testDraft->log) - 1]->toArray());
    }

    #[Test]
    public function itUpdatesCurrentPlayer(): void
    {
        $player1Id = PlayerId::fromString(array_keys($this->testDraft->players)[0]);
        $player2Id = PlayerId::fromString(array_keys($this->testDraft->players)[1]);
        $pickCmd = new PlayerPick(
            $this->testDraft,
            new Pick($player1Id, PickCategory::SLICE, '7'),
        );
        $pickCmd->handle();

        $this->reloadDraft();

        $this->assertSame($this->testDraft->currentPlayerId->value, $player2Id->value);
    }

    #[Test]
    #[DataProvider('picks')]
    public function itThrowsAnErrorWhenPickCategoryIsPicked(PickCategory $category, string $pick): void
    {
        $playerId = PlayerId::fromString(array_key_first($this->testDraft->players));
        $pickCmd = new PlayerPick($this->testDraft, new Pick($playerId, $category, $pick));
        $pickCmd->handle();

        $this->expectException(InvalidPickException::class);

        $pickCmd->handle();
    }

    #[Test]
    #[DataProvider('picks')]
    public function itThrowsAnErrorWhenPickWasAlreadyPicked(PickCategory $category, string $pick): void
    {
        $player1Id = PlayerId::fromString(array_key_first($this->testDraft->players));
        $player2Id = PlayerId::fromString(array_key_last($this->testDraft->players));
        $pickCmd = new PlayerPick($this->testDraft, new Pick($player1Id, $category, $pick));
        $pickCmd->handle();

        $this->expectException(InvalidPickException::class);

        $pick2Cmd = new PlayerPick($this->testDraft, new Pick($player2Id, $category, $pick));
        $pick2Cmd->handle();

    }
}