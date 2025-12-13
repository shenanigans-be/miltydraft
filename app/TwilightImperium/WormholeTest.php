<?php

namespace App\TwilightImperium;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class WormholeTest extends TestCase {

    public static function jsonData()
    {
        yield "When slice has 1 wormhole" => [
            "wormhole" => "alpha",
            "expected" => [Wormhole::ALPHA]
        ];
        yield "When slice has multiple wormholes" => [
            "wormhole" => "alpha-beta",
            "expected" => [Wormhole::ALPHA, Wormhole::BETA]
        ];
        yield "When slice has no wormholes" => [
            "wormhole" => null,
            "expected" => []
        ];
    }

    #[DataProvider("jsonData")]
    #[Test]
    public function itCanGetWormholesFromJsonData(?string $wormhole, array $expected)
    {
        $this->assertSame($expected, Wormhole::fromJsonData($wormhole));
    }
}