<?php

declare(strict_types=1);

namespace App\Draft;

use App\Testing\TestCase;
use App\Testing\TestDrafts;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

class PlayerTest extends TestCase
{
    #[DataProviderExternal(TestDrafts::class, 'provideTestDrafts')]
    #[Test]
    public function itCanBeInstantiatedFromJson($data): void
    {
        foreach($data['draft']['players'] as $playerData) {
            $p = Player::fromJson($playerData);
            $this->assertSame($playerData['id'], $p->id->value);
            $this->assertSame($playerData['name'], $p->name);
            $this->assertSame($playerData['faction'], $p->pickedFaction);
            $this->assertSame($playerData['slice'], $p->pickedSlice);
            $this->assertSame($playerData['position'], $p->pickedPosition);
        }
    }

    #[Test]
    public function itChecksIfPlayerHasPickedPosition(): void
    {
        $player1 = new Player(PlayerId::fromString('1'), 'Alice', false, '1');
        $player2 = new Player(PlayerId::fromString('2'), 'Bob');

        $this->assertTrue($player1->hasPickedPosition());
        $this->assertFalse($player2->hasPickedPosition());
    }

    #[Test]
    public function itChecksIfPlayerHasPickedFaction(): void
    {
        $player1 = new Player(PlayerId::fromString('1'), 'Alice', false, null, 'Mahact');
        $player2 = new Player(PlayerId::fromString('2'), 'Bob');

        $this->assertTrue($player1->hasPickedFaction());
        $this->assertFalse($player2->hasPickedFaction());
    }

    #[Test]
    public function itChecksIfPlayerHasPickedSlice(): void
    {
        $player1 = new Player(PlayerId::fromString('1'), 'Alice', false, null, null, '1');
        $player2 = new Player(PlayerId::fromString('2'), 'Bob');

        $this->assertTrue($player1->hasPickedSlice());
        $this->assertFalse($player2->hasPickedSlice());
    }

    #[Test]
    public function itCanBeConvertedToAnArray(): void
    {
        $player1 = new Player(
            PlayerId::fromString('1'),
            'Alice',
            true,
            '2',
            'Mahact',
            '3',
            'A',
        );
        $player2 = new Player(PlayerId::fromString('2'), 'Bob');

        $this->assertSame([
            'id' => '1',
            'name' => 'Alice',
            'claimed' => true,
            'position' => '2',
            'faction' => 'Mahact',
            'slice' => '3',
            'team' => 'A',
        ], $player1->toArray());
        $this->assertSame([
            'id' => '2',
            'name' => 'Bob',
            'claimed' => false,
            'position' => null,
            'faction' => null,
            'slice' => null,
            'team' => null,
        ], $player2->toArray());
    }

    public static function picks(): iterable
    {
        yield 'When picking slice' => [
            'category' => PickCategory::SLICE,
        ];

        yield 'When picking position' => [
            'category' => PickCategory::POSITION,
        ];

        yield 'When picking faction' => [
            'category' => PickCategory::FACTION,
        ];
    }

    #[Test]
    #[DataProvider('picks')]
    public function itCanPickSomething($category): void
    {
        $player = new Player(
            PlayerId::fromString('1'),
            'Alice',
            true,
            $category == PickCategory::POSITION ? null : '2',
            $category == PickCategory::FACTION ? null : 'Mahact',
            $category == PickCategory::SLICE ? null : '3',
            'A',
        );

        $newPlayerVo = $player->pick(new Pick(PlayerId::fromString('1'), $category, 'some-value'));

        $this->assertSame($player->id->value, $newPlayerVo->id->value);
        $this->assertSame($player->name, $newPlayerVo->name);
        $this->assertSame($player->claimed, $newPlayerVo->claimed);
        $this->assertSame($player->team, $newPlayerVo->team);

        switch($category) {
            case PickCategory::POSITION:
                $this->assertSame($player->pickedSlice, $newPlayerVo->pickedSlice);
                $this->assertSame($player->pickedFaction, $newPlayerVo->pickedFaction);
                $this->assertSame('some-value', $newPlayerVo->pickedPosition);

                break;
            case PickCategory::FACTION:
                $this->assertSame($player->pickedSlice, $newPlayerVo->pickedSlice);
                $this->assertSame($player->pickedPosition, $newPlayerVo->pickedPosition);
                $this->assertSame('some-value', $newPlayerVo->pickedFaction);

                break;
            case PickCategory::SLICE:
                $this->assertSame($player->pickedPosition, $newPlayerVo->pickedPosition);
                $this->assertSame($player->pickedFaction, $newPlayerVo->pickedFaction);
                $this->assertSame('some-value', $newPlayerVo->pickedSlice);

                break;
        }
    }
}