<?php

namespace App\Draft\Commands;

use App\Draft\Exceptions\InvalidClaimException;
use App\Draft\PlayerId;
use App\Testing\TestCase;
use App\Testing\UsesTestDraft;
use PHPUnit\Framework\Attributes\Test;

class UnclaimPlayerTest extends TestCase
{
    use UsesTestDraft;

    #[Test]
    public function itCanUnclaimAPlayer()
    {
        $playerId = PlayerId::fromString(array_key_first($this->testDraft->players));

        $this->testDraft->players[$playerId->value] = $this->testDraft->playerById($playerId)->claim();
        $this->testDraft->secrets->generateSecretForPlayer($playerId);

        $unclaimPlayer = new UnclaimPlayer($this->testDraft, $playerId);
        $unclaimPlayer->handle();

        // check to see if changes were saved
        $this->reloadDraft();

        $this->assertFalse($this->testDraft->playerById($playerId)->claimed);
        $this->assertNull($this->testDraft->secrets->secretById($playerId));

    }

    #[Test]
    public function itThrowsAnErrorIfPlayerIsNotPartOfDraft()
    {
        $playerId = PlayerId::fromString('123');

        $this->expectException(\Exception::class);
        $claimPlayer = new ClaimPlayer($this->testDraft, $playerId);
        $claimPlayer->handle();
    }


    #[Test]
    public function itThrowsAnErrorIfPlayerIsNotClaimed()
    {
        $playerId = PlayerId::fromString(array_key_first($this->testDraft->players));

        $unclaim = new UnclaimPlayer($this->testDraft, $playerId);

        $this->expectException(InvalidClaimException::class);
        $unclaim->handle();
    }


}