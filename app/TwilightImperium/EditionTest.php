<?php

namespace App\TwilightImperium;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class EditionTest extends TestCase
{
    #[Test]
    public function itCanCheckForValidTileSets()
    {
        foreach(Edition::cases() as $edition) {
            if ($edition == Edition::DISCORDANT_STARS) {
                $this->assertFalse(Edition::hasValidTileSet($edition));
            } else {
                $this->assertTrue(Edition::hasValidTileSet($edition));
            }
        }
    }
}