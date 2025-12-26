<?php

declare(strict_types=1);

namespace App\TwilightImperium;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SpaceStationTest extends TestCase
{
    #[Test]
    public function itcanCreateASpaceStationFromJsonData(): void {
        $jsonData = [
            'name' => 'Oluz Station',
            'resources' => 1,
            'influence' => 1,
        ];
        $spaceStation = SpaceStation::fromJsonData($jsonData);

        $this->assertSame($jsonData['name'], $spaceStation->name);
        $this->assertSame($jsonData['resources'], $spaceStation->resources);
        $this->assertSame($jsonData['influence'], $spaceStation->influence);
    }
}