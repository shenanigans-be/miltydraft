<?php

declare(strict_types=1);

namespace App\TwilightImperium;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AllianceTeamPositionTest extends TestCase
{
    // check if all values are present for backwards compatibility with old drafts
    #[Test]
    public function itHasAllAllianceTeamPositionValues(): void {
        $values = array_map(fn (AllianceTeamPosition $mode) => $mode->value, AllianceTeamPosition::cases());

        $this->assertContains('neighbors', $values);
        $this->assertContains('opposites', $values);
        $this->assertContains('none', $values);
    }
}