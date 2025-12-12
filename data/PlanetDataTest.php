<?php

namespace data;

use App\Game\Planet;
use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PlanetDataTest extends TestCase
{
    #[Test]
    public function allPlanetsInTilesJsonAreValid() {
        $tileData = json_decode(file_get_contents('data/tiles.json'), true);
        $planetJsonData = [];
        foreach($tileData as $t) {
            foreach($t['planets'] as $p) {
                $planetJsonData[] = $p;
            }
        }

        $planets = array_map(fn (array $data) => Planet::fromJsonData($data), $planetJsonData);

        $this->assertCount(count($planetJsonData), $planets);
    }
}