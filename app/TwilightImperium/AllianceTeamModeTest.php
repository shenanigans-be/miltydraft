<?php

declare(strict_types=1);

namespace App\TwilightImperium;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AllianceTeamModeTest extends TestCase
{
    // check if all values are present for backwards compatibility with old drafts
    #[Test]
    public function itHasAllAllianceTeamModeValues(): void {
        $values = array_map(fn (AllianceTeamMode $mode) => $mode->value, AllianceTeamMode::cases());

        $this->assertContains('random', $values);
        $this->assertContains('preset', $values);
    }
}