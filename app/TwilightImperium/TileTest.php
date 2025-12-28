<?php

declare(strict_types=1);

namespace App\TwilightImperium;

use App\Testing\Factories\TileFactory;
use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TileTest extends TestCase
{
    #[Test]
    public function itCalculatesTotalValues(): void
    {
        $planets = [
            new Planet('test', 4, 2),
            new Planet('test', 3, 3),
        ];

        $tile = TileFactory::make($planets);

        $this->assertSame(7, $tile->totalResources);
        $this->assertSame(5, $tile->totalInfluence);
        $this->assertSame(1.5, $tile->optimalInfluence);
        $this->assertSame(5.5, $tile->optimalResources);
        $this->assertSame(7.0, $tile->optimalTotal);
    }

    public static function jsonData() {
        yield  'For a regular tile' => [
            'jsonData' => [
                'type' => 'red',
                'wormhole' => null,
                'anomaly' => null,
                'planets' => [],
                'stations' => [],
                'set' => Edition::BASE_GAME->value,
            ],
            'expectedWormholes' => [],
        ];
        yield  'For a tile with a wormhole' => [
            'jsonData' => [
                'type' => 'blue',
                'wormhole' => 'gamma',
                'anomaly' => null,
                'planets' => [],
                'stations' => [],
                'set' => Edition::PROPHECY_OF_KINGS->value,
            ],
            'expectedWormholes' => [Wormhole::GAMMA],
        ];
        yield  'For a tile with an anomaly' => [
            'jsonData' => [
                'type' => 'green',
                'wormhole' => null,
                'anomaly' => 'nebula',
                'planets' => [],
                'stations' => [],
                'set' => Edition::THUNDERS_EDGE->value,
            ],
            'expectedWormholes' => [],
        ];
        yield  'For a tile with no stations property' => [
            'jsonData' => [
                'type' => 'red',
                'wormhole' => null,
                'anomaly' => null,
                'planets' => [],
                'set' => Edition::DISCORDANT_STARS->value,
            ],
            'expectedWormholes' => [],
        ];
        yield  'For a tile with planets' => [
            'jsonData' => [
                'type' => 'red',
                'wormhole' => null,
                'anomaly' => null,
                'planets' => [
                    [
                        'name' => 'Tinnes',
                        'resources' => 2,
                        'influence' => 1,
                        'trait' => 'hazardous',
                        'legendary' => false,
                        'specialties' => [],
                    ],
                    [
                        'name' => 'Tinnes 2',
                        'resources' => 1,
                        'influence' => 1,
                        'trait' => 'industrial',
                        'legendary' => false,
                        'specialties' => [],
                    ],
                ],
                'set' => Edition::DISCORDANT_STARS_PLUS->value,
            ],
            'expectedWormholes' => [],
        ];
        yield  'For a tile with stations' => [
            'jsonData' => [
                'type' => 'red',
                'wormhole' => null,
                'anomaly' => null,
                'planets' => [],
                'stations' => [
                    [
                        'name' => 'Tinnes',
                        'resources' => 2,
                        'influence' => 1,
                    ],
                    [
                        'name' => 'Tinnes 2',
                        'resources' => 1,
                        'influence' => 1,
                    ],
                ],
                'set' => Edition::THUNDERS_EDGE->value,
            ],
            'expectedWormholes' => [],
        ];
        yield  'For a tile with hyperlanes' => [
            'jsonData' => [
                'type' => 'red',
                'wormhole' => null,
                'anomaly' => null,
                'planets' => [],
                'hyperlanes' => [
                    [
                        0,
                        3,
                    ],
                    [
                        0,
                        2,
                    ],
                ],
                'set' => Edition::PROPHECY_OF_KINGS->value,
            ],
            'expectedWormholes' => [],
        ];
    }

    #[DataProvider('jsonData')]
    #[Test]
    public function itCanBeInitializedFromJsonData(array $jsonData, array $expectedWormholes): void {
        $id = 'tile-id';

        $tile = Tile::fromJsonData($id, TileTier::MEDIUM, $jsonData);
        $this->assertSame($id, $tile->id);
        $this->assertSame($jsonData['anomaly'], $tile->anomaly);
        $this->assertSame($jsonData['hyperlanes'] ?? [], $tile->hyperlanes);
        $this->assertSame($expectedWormholes, $tile->wormholes);
    }

    public static function anomalies() {
        yield 'When tile has anomaly' => [
            'anomaly' => 'nebula',
            'expected' => true,
        ];
        yield 'When tile has no anomaly' => [
            'anomaly' => null,
            'expected' => false,
        ];
    }

    #[DataProvider('anomalies')]
    #[Test]
    public function itCanCheckForAnomalies(?string $anomaly, bool $expected): void {
        $tile = TileFactory::make([], [], $anomaly);

        $this->assertSame($expected, $tile->hasAnomaly());
    }

    public static function wormholeTiles() {
        yield 'When tile has wormhole' => [
            'lookingFor' => Wormhole::ALPHA,
            'hasWormholes' => [Wormhole::ALPHA],
            'expected' => true,
        ];
        yield 'When tile has multiple wormholes' => [
            'lookingFor' => Wormhole::BETA,
            'hasWormholes' => [Wormhole::ALPHA, Wormhole::BETA],
            'expected' => true,
        ];
        yield 'When tile does not have wormhole' => [
            'lookingFor' => Wormhole::EPSILON,
            'hasWormholes' => [Wormhole::GAMMA],
            'expected' => false,
        ];
        yield 'When tile has no wormholes' => [
            'lookingFor' => Wormhole::DELTA,
            'hasWormholes' => [],
            'expected' => false,
        ];
    }

    #[DataProvider('wormholeTiles')]
    #[Test]
    public function itCanCheckForWormholes(Wormhole $lookingFor, array $hasWormholes, bool $expected): void {
        $tile = TileFactory::make([], $hasWormholes);

        $this->assertSame($expected, $tile->hasWormhole($lookingFor));
    }

    #[Test]
    public function itCanCheckForLegendaryPlanets(): void {
        $regularPlanet = new Planet('regular', 1, 1);
        $legendaryPlanet = new Planet('legendary', 3, 3, 'Legend has it...');

        $tileWithLegendary = TileFactory::make([
            $regularPlanet,
            $legendaryPlanet,
        ]);
        $tileWithoutLegendary = TileFactory::make([
            $regularPlanet,
        ]);

        $this->assertTrue($tileWithLegendary->hasLegendaryPlanet());
        $this->assertFalse($tileWithoutLegendary->hasLegendaryPlanet());
    }

    public static function tiles()
    {
        yield 'When tile has nothing special' => [
            'tile' => TileFactory::make(),
            'expected' => [
                'alpha' => 0,
                'beta' => 0,
                'legendary' => 0,
            ],
        ];
        yield 'When tile has wormhole' => [
            'tile' => TileFactory::make([], [Wormhole::ALPHA]),
            'expected' => [
                'alpha' => 1,
                'beta' => 0,
                'legendary' => 0,
            ],
        ];
        yield 'When tile has multiple wormholes' => [
            'tile' => TileFactory::make([], [Wormhole::ALPHA, Wormhole::BETA]),
            'expected' => [
                'alpha' => 1,
                'beta' => 1,
                'legendary' => 0,
            ],
        ];
        yield 'When tile has legendary planet' => [
            'tile' => TileFactory::make([
                    new Planet('test', 0, 0, 'yes'),
            ]),
            'expected' => [
                'alpha' => 0,
                'beta' => 0,
                'legendary' => 1,
            ],
        ];
        yield 'When tile has wormhole and legendary' => [
            'tile' => TileFactory::make(
                [new Planet('test', 0, 0, 'yes')],
                [Wormhole::BETA],
            ),
            'expected' => [
                'alpha' => 0,
                'beta' => 1,
                'legendary' => 1,
            ],
        ];
    }

    #[DataProvider('tiles')]
    #[Test]
    public function itCanCountSpecials(Tile $tile, array $expected): void {
        $count = Tile::countSpecials([$tile]);
        $this->assertSame($expected['alpha'], $count['alpha']);
        $this->assertSame($expected['beta'], $count['beta']);
        $this->assertSame($expected['legendary'], $count['legendary']);
    }

    // just to be sure
    #[DataProvider('tiles')]
    #[Test]
    public function itCanCountSpecialsForMultipleTiles(Tile $tile, array $expected): void {
        $count = Tile::countSpecials([$tile, $tile]);
        $this->assertSame($expected['alpha'] * 2, $count['alpha']);
        $this->assertSame($expected['beta'] * 2, $count['beta']);
        $this->assertSame($expected['legendary'] * 2, $count['legendary']);
    }

    public static function allJsonTiles(): iterable
    {
        $tileJsonData = json_decode(file_get_contents('data/tiles.json'), true);
        foreach($tileJsonData as $key => $tileData) {
            yield 'Tile #' . $key => [
                'key' => $key,
                'tileData' => $tileData,
            ];
        }
    }

    #[Test]
    #[DataProvider('allJsonTiles')]
    public function tilesCanBeFetchedFromJson($key, $tileData): void
    {
        $tiles = Tile::all();
        $this->assertArrayHasKey($key, $tiles);
        // the "toJson" tests should cover the rest, we're just making sure it can fetch all the tiles
    }

}