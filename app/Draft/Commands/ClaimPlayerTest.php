<?php

declare(strict_types=1);

namespace App\Draft\Commands;

use App\Draft\Exceptions\InvalidClaimException;
use App\Draft\PlayerId;
use App\Shared\Command;
use App\Testing\TestCase;
use App\Testing\UsesTestDraft;
use PHPUnit\Framework\Attributes\Test;

class ClaimPlayerTest extends TestCase
{
    use UsesTestDraft;

    #[Test]
    public function itImplementsCommand(): void
    {
        $playerId = PlayerId::fromString(array_key_first($this->testDraft->players));
        $claimPlayer = new ClaimPlayer($this->testDraft, $playerId);

        $this->assertInstanceOf(Command::class, $claimPlayer);
    }

    #[Test]
    public function itCanClaimAPlayer(): void
    {
        $playerId = PlayerId::fromString(array_key_first($this->testDraft->players));

        $claimPlayer = new ClaimPlayer($this->testDraft, $playerId);

        $secret = $claimPlayer->handle();

        // check to see if changes were saved
        $this->reloadDraft();

        $this->assertTrue($this->testDraft->playerById($playerId)->claimed);
        $this->assertTrue($this->testDraft->secrets->checkPlayerSecret($playerId, $secret));
    }

    #[Test]
    public function itThrowsAnErrorIfPlayerIsNotPartOfDraft(): void
    {
        $playerId = PlayerId::fromString('123');

        $this->expectException(\Exception::class);
        $claimPlayer = new ClaimPlayer($this->testDraft, $playerId);
        $claimPlayer->handle();
    }

    #[Test]
    public function itThrowsAnErrorIfPlayerIsAlreadyClaimed(): void
    {
        $playerId = PlayerId::fromString(array_key_first($this->testDraft->players));

        $claimPlayer = new ClaimPlayer($this->testDraft, $playerId);
        $claimPlayer->handle();

        $this->expectException(InvalidClaimException::class);
        $claimPlayer->handle();
    }

}