<?php

namespace App\Draft;

use App\Testing\TestCase;
use App\Testing\TestDrafts;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

class PlayerTest extends TestCase
{
    #[DataProviderExternal(TestDrafts::class, "provideTestDrafts")]
    #[Test]
    public function itCanBeInstantiatedFromJson($data)
    {
        foreach($data['draft']['players'] as $playerData) {
            $p = Player::fromJson($playerData);
            $this->assertSame($playerData['id'], $p->id);
            $this->assertSame($playerData['name'], $p->name);
            $this->assertSame($playerData['faction'], $p->pickedFaction);
            $this->assertSame($playerData['slice'], $p->pickedSlice);
            $this->assertSame($playerData['position'], $p->pickedPosition);
        }
    }

    #[Test]
    public function itChecksIfPlayerHasPickedPosition()
    {
        $player1 = new Player('1', 'Alice', false, "1");
        $player2 = new Player('2', 'Bob');

        $this->assertTrue($player1->hasPickedPosition());
        $this->assertFalse($player2->hasPickedPosition());
    }

    #[Test]
    public function itChecksIfPlayerHasPickedFaction()
    {
        $player1 = new Player('1', 'Alice', false, null, "Mahact");
        $player2 = new Player('2', 'Bob');

        $this->assertTrue($player1->hasPickedFaction());
        $this->assertFalse($player2->hasPickedFaction());
    }

    #[Test]
    public function itChecksIfPlayerHasPickedSlice()
    {
        $player1 = new Player('1', 'Alice', false, null, null, "1");
        $player2 = new Player('2', 'Bob');

        $this->assertTrue($player1->hasPickedSlice());
        $this->assertFalse($player2->hasPickedSlice());
    }


    #[Test]
    public function itCanBeConvertedToAnArray()
    {
        $player1 = new Player(
            '1',
            'Alice',
            true,
            '2',
            "Mahact",
            '3',
            'A'
        );
        $player2 = new Player('2', 'Bob');

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
            'team' => null
        ], $player2->toArray());
    }
}