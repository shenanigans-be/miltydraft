<?php

namespace App\Draft;

use App\Draft\Exceptions\InvalidSliceException;
use App\Testing\Factories\PlanetFactory;
use App\Testing\Factories\TileFactory;
use App\Testing\TestCase;
use App\TwilightImperium\Planet;
use App\TwilightImperium\Wormhole;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class SliceTest extends TestCase
{
    #[Test]
    public function itCalculatesTotalAndOptimalValues()
    {
        $planets = [
            PlanetFactory::make([
                'resources' => 4,
                'influence' => 2
            ]),  // optimal: 4, 0
            PlanetFactory::make([
                'resources' => 3,
                'influence' => 3
            ]), // optimal: 1.5, 1.5
            PlanetFactory::make([
                'resources' => 1,
                'influence' => 0
            ]), // optimal: 1, 0
            PlanetFactory::make([
                'resources' => 1,
                'influence' => 2
            ]), // optimal: 0, 2
        ];

        $totalInfluence = array_reduce($planets, fn ($sum, Planet $p) => $sum += $p->influence);
        $totalResources = array_reduce($planets, fn ($sum, Planet $p) => $sum += $p->resources);
        $optimalInfluence = array_reduce($planets, fn ($sum, Planet $p) => $sum += $p->optimalInfluence);
        $optimalResources = array_reduce($planets, fn ($sum, Planet $p) => $sum += $p->optimalResources);


        $slice = new Slice([
            TileFactory::make([$planets[0], $planets[1]]),
            TileFactory::make([$planets[2], $planets[3]]),
            TileFactory::make(),
            TileFactory::make(),
            TileFactory::make(),
        ]);

        $this->assertSame($totalResources, $slice->totalResources);
        $this->assertSame($totalInfluence, $slice->totalInfluence);
        $this->assertSame($optimalResources, $slice->optimalResources);
        $this->assertSame($optimalInfluence, $slice->optimalInfluence);
        $this->assertSame($optimalResources + $optimalInfluence, $slice->optimalTotal);
    }

    public static function tileConfigurations(): iterable
    {
        yield "When it has no anomalies" => [
            "tiles" => [
                TileFactory::make([], [], null),
                TileFactory::make([], [], null),
                TileFactory::make([], [], null),
                TileFactory::make([], [], null),
                TileFactory::make([], [], null),
            ],
            "canBeArranged" => true
        ];
        yield "When it has some anomalies" => [
            "tiles" => [
                TileFactory::make([], [], "nebula"),
                TileFactory::make([], [], "asteroid field"),
                TileFactory::make([], [], null),
                TileFactory::make([], [], null),
                TileFactory::make([], [], null),
            ],
            "canBeArranged" => true
        ];
        yield "When it has too many anomalies" => [
            "tiles" => [
                TileFactory::make([], [], "nebula"),
                TileFactory::make([], [], "asteroid field"),
                TileFactory::make([], [], "gravity-rift"),
                TileFactory::make([], [], "supernova"),
                TileFactory::make([], [], null),
            ],
            "canBeArranged" => false
        ];
    }

    #[DataProvider("tileConfigurations")]
    #[Test]
    public function itCanArrangeTiles(array $tiles, bool $canBeArranged)
    {
        if (!$canBeArranged) {
            $this->expectException(InvalidSliceException::class);
        }
        $slice = new Slice($tiles);
        $seed = new Seed(1);

        $slice->arrange($seed);

        $this->assertTrue($slice->tileArrangementIsValid());
    }

    #[Test]
    public function itWontAllowSlicesWithTooManyWormholes()
    {
        $slice = new Slice([
            TileFactory::make([], [Wormhole::ALPHA]),
            TileFactory::make([], [Wormhole::ALPHA]),
            TileFactory::make(),
            TileFactory::make(),
            TileFactory::make(),
        ]);

        $this->expectException(InvalidSliceException::class);

        $slice->validate(0, 0, 0, 0, null);
    }

    #[Test]
    public function itWontAllowSlicesWithTooManyLegendaryPlanets()
    {
        $slice = new Slice([
            TileFactory::make([PlanetFactory::make(['legendary' => 'Yes'])]),
            TileFactory::make([PlanetFactory::make(['legendary' => 'Yes'])]),
            TileFactory::make(),
            TileFactory::make(),
            TileFactory::make(),
        ]);

        $this->expectException(InvalidSliceException::class);

        $slice->validate(0, 0, 0, 0, null);
    }

    #[Test]
    public function itCanValidateMaxWormholes()
    {
        $slice = new Slice([
            TileFactory::make([], [Wormhole::ALPHA]),
            TileFactory::make([], [Wormhole::BETA]),
            TileFactory::make(),
            TileFactory::make(),
            TileFactory::make(),
        ]);

        $this->expectException(InvalidSliceException::class);

        $slice->validate(0, 0, 0, 0, 1);
    }

    #[Test]
    public function itCanValidateMinimumOptimalInfluence()
    {
        $slice = new Slice([
            TileFactory::make([
                PlanetFactory::make([
                    'influence' => 2,
                    'resources' => 3
                ])
            ]),
            TileFactory::make([
                PlanetFactory::make([
                    'influence' => 1,
                    'resources' => 0
                ])
            ]),
            TileFactory::make(),
            TileFactory::make(),
            TileFactory::make(),
        ]);

        $this->expectException(InvalidSliceException::class);

        $slice->validate(
            2,
            0,
            0,
            0,
            1
        );
    }

    #[Test]
    public function itCanValidateMinimumOptimalResources()
    {
        $slice = new Slice([
            TileFactory::make([
                PlanetFactory::make([
                    'influence' => 5,
                    'resources' => 2
                ])
            ]),
            TileFactory::make([
                PlanetFactory::make([
                    'influence' => 1,
                    'resources' => 1
                ])
            ]),
            TileFactory::make(),
            TileFactory::make(),
            TileFactory::make(),
        ]);

        $this->expectException(InvalidSliceException::class);

        $slice->validate(
            0,
            3,
            0,
            0,
        );
    }

    #[Test]
    public function itCanValidateMinimumOptimalTotal()
    {
        $slice = new Slice([
            TileFactory::make([
                PlanetFactory::make([
                    'influence' => 4,
                    'resources' => 2
                ])
            ]),
            TileFactory::make([
                PlanetFactory::make([
                    'influence' => 1,
                    'resources' => 1
                ])
            ]),
            TileFactory::make(),
            TileFactory::make(),
            TileFactory::make(),
        ]);

        $this->expectException(InvalidSliceException::class);

        $slice->validate(
            0,
            0,
            5,
            0
        );
    }

    #[Test]
    public function itCanValidateMaximumOptimalTotal()
    {
        $slice = new Slice([
            TileFactory::make([
                PlanetFactory::make([
                    'influence' => 2,
                    'resources' => 4
                ])
            ]),
            TileFactory::make([
                PlanetFactory::make([
                    'influence' => 2,
                    'resources' => 1
                ])
            ]),
            TileFactory::make([
                PlanetFactory::make([
                    'influence' => 3,
                    'resources' => 1
                ])
            ]),
            TileFactory::make(),
            TileFactory::make(),
        ]);

        $this->expectException(InvalidSliceException::class);

        $slice->validate(
            0,
            0,
            0,
            4
        );
    }


    #[Test]
    public function itCanValidateAValidSlice()
    {
        $slice = new Slice([
            TileFactory::make([
                PlanetFactory::make([
                    'influence' => 2,
                    'resources' => 3,
                ])
            ]),
            TileFactory::make([
                PlanetFactory::make([
                    'influence' => 2,
                    'resources' => 1
                ])
            ]),
            TileFactory::make([
                PlanetFactory::make([
                    'influence' => 1,
                    'resources' => 1
                ]),
                PlanetFactory::make([
                    'influence' => 1,
                    'resources' => 1
                ])
            ]),
            TileFactory::make(),
            TileFactory::make(),
        ]);

        $valid = $slice->validate(
            1,
            3,
            5,
            7
        );

        $this->assertTrue($valid);
    }
}