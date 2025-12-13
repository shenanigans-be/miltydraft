<?php

namespace App\TwilightImperium;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TileTest extends TestCase
{
    #[Test]
    public function itCalculatesTotalValues()
    {
        $planets = [
            new Planet('test',4, 2),
            new Planet('test',3, 3),
        ];

        $tile = new Tile(
            "test",
            TileType::BLUE,
            $planets,
            [],
        );

        $this->assertSame(7, $tile->totalResources);
        $this->assertSame(5, $tile->totalInfluence);
        $this->assertSame(1.5, $tile->optimalInfluence);
        $this->assertSame(5.5, $tile->optimalResources);
        $this->assertSame(7.0, $tile->optimalTotal);
    }


    public static function jsonData() {
        yield  "For a regular tile" => [
            "jsonData" => [
                "type" => "red",
                "wormhole" => null,
                "anomaly" => null,
                "planets" => [],
                "stations" => []
            ],
            "expectedWormholes" => [],
        ];
        yield  "For a tile with a wormhole" => [
            "jsonData" => [
                "type" => "blue",
                "wormhole" => "gamma",
                "anomaly" => null,
                "planets" => [],
                "stations" => []
            ],
            "expectedWormholes" => [Wormhole::GAMMA],
        ];
        yield  "For a tile with an anomaly" => [
            "jsonData" => [
                "type" => "green",
                "wormhole" => null,
                "anomaly" => "nebula",
                "planets" => [],
                "stations" => []
            ],
            "expectedWormholes" => [],
        ];
        yield  "For a tile with no stations property" => [
            "jsonData" => [
                "type" => "red",
                "wormhole" => null,
                "anomaly" => null,
                "planets" => []
            ],
            "expectedWormholes" => [],
        ];
        yield  "For a tile with planets" => [
            "jsonData" => [
                "type" => "red",
                "wormhole" => null,
                "anomaly" => null,
                "planets" => [
                    [
                        "name" => "Tinnes",
                        "resources" =>  2,
                        "influence" => 1,
                        "trait" => "hazardous",
                        "legendary" => false,
                        "specialties" => []
                    ],
                    [
                        "name" => "Tinnes 2",
                        "resources" =>  1,
                        "influence" => 1,
                        "trait" => "industrial",
                        "legendary" => false,
                        "specialties" => []
                    ]
                ]
            ],
            "expectedWormholes" => [],
        ];
        yield  "For a tile with stations" => [
            "jsonData" => [
                "type" => "red",
                "wormhole" => null,
                "anomaly" => null,
                "planets" => [],
                "stations" => [
                    [
                        "name" => "Tinnes",
                        "resources" =>  2,
                        "influence" => 1,
                    ],
                    [
                        "name" => "Tinnes 2",
                        "resources" =>  1,
                        "influence" => 1,
                    ]
                ]
            ],
            "expectedWormholes" => [],
        ];
        yield  "For a tile with hyperlanes" => [
            "jsonData" => [
                "type" => "red",
                "wormhole" => null,
                "anomaly" => null,
                "planets" => [],
                "hyperlanes" => [
                    [
                        0,
                        3
                    ],
                    [
                        0,
                        2
                    ],
                ]
            ],
            "expectedWormholes" => [],
        ];
    }

    #[DataProvider("jsonData")]
    #[Test]
    public function itCanBeInitializedFromJsonData(array $jsonData, array $expectedWormholes) {
        $id = "tile-id";

        $tile = Tile::fromJsonData($id, $jsonData);
        $this->assertSame($id, $tile->id);
        $this->assertSame($jsonData['anomaly'], $tile->anomaly);
        $this->assertSame($jsonData['hyperlanes'] ?? [], $tile->hyperlanes);
        $this->assertSame($expectedWormholes, $tile->wormholes);
    }


    public static function anomalies() {
        yield "When tile has anomaly" => [
            "anomaly" => "nebula",
            "expected" => true
        ];
        yield "When tile has no anomaly" => [
            "anomaly" => null,
            "expected" => false
        ];
    }

    #[DataProvider("anomalies")]
    #[Test]
    public function itCanCheckForAnomalies(?string $anomaly, bool $expected) {
        $tile = new Tile(
            "test-with-anomaly",
            TileType::BLUE,
            [],
            [],
            [],
            $anomaly
        );

        $this->assertSame($expected, $tile->hasAnomaly());
    }

    public static function wormholeTiles() {
        yield "When tile has wormhole" => [
            "lookingFor" => Wormhole::ALPHA,
            "has" => [Wormhole::ALPHA],
            "expected" => true
        ];
        yield "When tile has multiple wormholes" => [
            "lookingFor" => Wormhole::BETA,
            "has" => [Wormhole::ALPHA, Wormhole::BETA],
            "expected" => true
        ];
        yield "When tile does not have wormhole" => [
            "lookingFor" => Wormhole::EPSILON,
            "has" => [Wormhole::GAMMA],
            "expected" => false
        ];
        yield "When tile has no wormholes" => [
            "lookingFor" => Wormhole::DELTA,
            "has" => [],
            "expected" => false
        ];
    }

    #[DataProvider('wormholeTiles')]
    #[Test]
    public function itCanCheckForWormholes(Wormhole $lookingFor, array $has, bool $expected) {
        $tile = new Tile(
            "test",
            TileType::BLUE,
            [],
            [],
            $has
        );

        $this->assertSame($expected, $tile->hasWormhole($lookingFor));
    }

    #[Test]
    public function itCanCheckForLegendaryPlanets() {
        $regularPlanet = new Planet("regular", 1, 1);
        $legendaryPlanet = new Planet("legendary", 3, 3, "Legend has it...");

        $tileWithLegendary = new Tile("with-legendary", TileType::GREEN, [
            $regularPlanet,
            $legendaryPlanet
        ]);
        $tileWithoutLegendary = new Tile("without-legendary", TileType::GREEN, [
            $regularPlanet
        ]);

        $this->assertTrue($tileWithLegendary->hasLegendaryPlanet());
        $this->assertFalse($tileWithoutLegendary->hasLegendaryPlanet());
    }

    public static function tiles()
    {
        yield "When tile has nothing special" => [
            "tile" => new Tile(
                "regular-tile",
                TileType::BLUE,
            ),
            "expected" => [
                "alpha" => 0,
                "beta" => 0,
                "legendary" => 0
            ]
        ];
        yield "When tile has wormhole" => [
            "tile" => new Tile(
                "regular-tile",
                TileType::BLUE,
                [],
                [],
                [Wormhole::ALPHA]
            ),
            "expected" => [
                "alpha" => 1,
                "beta" => 0,
                "legendary" => 0
            ]
        ];
        yield "When tile has multiple wormholes" => [
            "tile" => new Tile(
                "regular-tile",
                TileType::BLUE,
                [],
                [],
                [Wormhole::ALPHA, Wormhole::BETA]
            ),
            "expected" => [
                "alpha" => 1,
                "beta" => 1,
                "legendary" => 0
            ]
        ];
        yield "When tile has legendary" => [
            "tile" => new Tile(
                "regular-tile",
                TileType::BLUE,
                [
                    new Planet("test", 0, 0, "yes")
                ]
            ),
            "expected" => [
                "alpha" => 0,
                "beta" => 0,
                "legendary" => 1
            ]
        ];
        yield "When tile has wormhole and legendary" => [
            "tile" => new Tile(
                "regular-tile",
                TileType::BLUE,
                [
                    new Planet("test", 0, 0, "yes")
                ],
                [],
                [Wormhole::BETA]
            ),
            "expected" => [
                "alpha" => 0,
                "beta" => 1,
                "legendary" => 1
            ]
        ];
    }

    #[DataProvider("tiles")]
    #[Test]
    public function itCanCountSpecials(Tile $tile, array $expected) {
        $count =  Tile::countSpecials([$tile]);
        $this->assertSame($expected["alpha"], $count["alpha"]);
        $this->assertSame($expected["beta"], $count["beta"]);
        $this->assertSame($expected["legendary"], $count["legendary"]);
    }

    // just to be sure
    #[DataProvider("tiles")]
    #[Test]
    public function itCanCountSpecialsForMultipleTiles(Tile $tile, array $expected) {
        $count =  Tile::countSpecials([$tile, $tile]);
        $this->assertSame($expected["alpha"] * 2, $count["alpha"]);
        $this->assertSame($expected["beta"] * 2, $count["beta"]);
        $this->assertSame($expected["legendary"] * 2, $count["legendary"]);
    }
}