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

    /**
     * If we ever change tilenames again (bad idea, turns out) these next test should prevent us from breaking old drafts
     *
     * @return void
     */
    #[Test]
    public function allHistoricTileIdsHaveData() {
        $historicTileIds = json_decode(file_get_contents('data/all-tiles-ever.json'));

        $currentTileIds = array_keys($this->getJsonData());

        foreach($historicTileIds as $id) {
            $this->assertContains($id, $currentTileIds);
        }
    }

    #[Test]
    public function allHistoricTileIdsHaveImages() {
        $historicTileIds = json_decode(file_get_contents('data/all-tiles-ever.json'));

        foreach($historicTileIds as $id) {
            $this->assertFileExists('img/tiles/ST_' . $id . '.png');
        }
    }
}