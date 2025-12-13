<?php

namespace App\TwilightImperium;

use App\Testing\FakesData;
use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class SpaceStationTest extends TestCase
{
    #[Test]
    public function itcanCreateASpaceStationFromJsonData() {
        $jsonData = [
            "name" => "Oluz Station",
            "resources" =>  1,
            "influence" => 1,
        ];
        $spaceStation = SpaceStation::fromJsonData($jsonData);

        $this->assertSame($jsonData['name'], $spaceStation->name);
        $this->assertSame($jsonData['resources'], $spaceStation->resources);
        $this->assertSame($jsonData['influence'], $spaceStation->influence);
    }
}