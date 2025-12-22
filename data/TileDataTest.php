<?php

namespace data;

use App\TwilightImperium\Planet;
use App\TwilightImperium\SpaceStation;
use App\Testing\TestCase;
use App\TwilightImperium\Tile;
use App\TwilightImperium\TileTier;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TileDataTest extends TestCase
{
    protected static function getJsonData(): array
    {
        return json_decode(file_get_contents('data/tiles.json'), true);
    }


    public static function allJsonTiles()
    {
        foreach(self::getJsonData() as $key => $tileData) {
            yield "Tile #" . $key => [
                'id' => $key,
                'data' => $tileData
            ];
        }
    }

    #[Test]
    #[DataProvider('allJsonTiles')]
    public function allPlanetsInTilesJsonAreValid($id, $data) {
        $planetJsonData = [];
        if (isset($data['planets'])) {
            foreach($data['planets'] as $p) {
                $planetJsonData[] = $p;
            }
        }

        $planets = array_map(fn (array $data) => Planet::fromJsonData($data), $planetJsonData);

        if (empty($planetJsonData)) {
            $this->expectNotToPerformAssertions();
        } else {
            $this->assertCount(count($planetJsonData), $planets);
        }

    }

    #[Test]
    #[DataProvider('allJsonTiles')]
    public function allSpaceStationsInTilesJsonAreValid($id, $data) {
        $spaceStationData = $data['stations'] ?? [];

        $spaceStations = array_map(fn (array $data) => SpaceStation::fromJsonData($data), $spaceStationData);

        if (empty($spaceStationData)) {
            $this->expectNotToPerformAssertions();
        } else {
            $this->assertCount(count($spaceStationData), $spaceStations);
        }
    }

    /**
     * If we ever change tilenames again (bad idea, turns out) these next test should prevent us from breaking old drafts
     *
     * @return void
     */
    #[Test]
    public function allHistoricTileIdsHaveData()
    {
        $historicTileIds = json_decode(file_get_contents('data/historic-test-data/all-tiles-ever.json'));

        $currentTileIds = array_keys(self::getJsonData());

        foreach($historicTileIds as $id) {
            $this->assertContains($id, $currentTileIds);
        }
    }

    #[Test]
    #[DataProvider('allJsonTiles')]
    public function allBlueTilesAreInTiers($id, $data)
    {
        $tileTiers = Tile::tierData();

        $isMecRexOrMallice = count($data['planets']) > 0 &&
            ($data['planets'][0]['name'] == "Mecatol Rex" || $data['planets'][0]['name'] == "Mallice");


        if ($data['type'] == "blue" && !$isMecRexOrMallice) {
            $this->assertArrayHasKey($id, $tileTiers);
        } else {
            $this->expectNotToPerformAssertions();
        }
    }

    #[Test]
    #[DataProvider('allJsonTiles')]
    public function tilesCanBeFetchedFromJson($id, $data)
    {
        // we're ignoring the tier for now, that's for TileTest
        $tile = Tile::fromJsonData($id,TileTier::MEDIUM, $data);
        $this->assertSame($data['anomaly'] ?? null, $tile->anomaly);
    }

    /**
     * This is a really useful test, but first the tilenames need to be sorted out
     **/
//    public function allHistoricTileIdsHaveImages() {
//        $historicTileIds = json_decode(file_get_contents('data/all-tiles-ever.json'));
//        $tiles = self::getJsonData();
//
//        foreach($historicTileIds as $id) {
//            if (!isset($tiles[$id]['faction'])) {
//                $this->assertFileExists('img/tiles/ST_' . $id . '.png');
//            }
//        }
//    }
}