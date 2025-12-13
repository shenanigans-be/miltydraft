<?php

namespace data;

use App\TwilightImperium\Planet;
use App\TwilightImperium\SpaceStation;
use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TileDataTest extends TestCase
{
    protected function getJsonData(): array
    {
        return json_decode(file_get_contents('data/tiles.json'), true);
    }

    #[Test]
    public function allPlanetsInTilesJsonAreValid() {
        $planetJsonData = [];
        foreach($this->getJsonData() as $t) {
            if (isset($t['planets'])) {
                foreach($t['planets'] as $p) {
                    $planetJsonData[] = $p;
                }
            }
        }

        $planets = array_map(fn (array $data) => Planet::fromJsonData($data), $planetJsonData);

        $this->assertCount(count($planetJsonData), $planets);
    }

    #[Test]
    public function allSpaceStationsInTilesJsonAreValid() {
        $spaceStationData = [];
        foreach($this->getJsonData() as $t) {
            if (isset($t['stations'])) {
                foreach($t['stations'] as $p) {
                    $spaceStationData[] = $p;
                }
            }
        }

        $spaceStations = array_map(fn (array $data) => SpaceStation::fromJsonData($data), $spaceStationData);

        $this->assertCount(count($spaceStationData), $spaceStations);
    }
}