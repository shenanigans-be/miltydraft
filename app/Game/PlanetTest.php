<?php

namespace App\Game;

use App\Testing\FakesData;
use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class PlanetTest extends TestCase
{
    use FakesData;

    public static function planetValues(): iterable {
        yield "when resource value is higher than influence" => [
            "resources" => 3,
            "influence" => 1,
            "expectedOptimalResources" => 3.0,
            "expectedOptimalInfluence" => 0.0
        ];
        yield "when resource value is lower than influence" => [
            "resources" => 2,
            "influence" => 4,
            "expectedOptimalResources" => 0.0,
            "expectedOptimalInfluence" => 4.0
        ];
        yield "when resource value equals influence" => [
            "resources" => 3,
            "influence" => 3,
            "expectedOptimalResources" => 1.5,
            "expectedOptimalInfluence" => 1.5
        ];
    }

    public static function jsonData(): iterable {
        yield "A planet without a legendary" => [
            "jsonData" => [
                "name" => "Tinnes",
                "resources" =>  2,
                "influence" => 1,
                "trait" => "hazardous",
                "legendary" => false,
                "specialties" => [
                    "biotic",
                    "cybernetic"
                ]
            ],
            "expectedLegendary" => null,
            "expectedTraits" => [
                PlanetTrait::HAZARDOUS
            ],
            "expectedTechSpecialties" => [
                TechSpecialties::BIOTIC,
                TechSpecialties::CYBERNETIC
            ]
        ];
        yield "A planet with a legendary" => [
            "jsonData" => [
                "name" => "Tinnes",
                "resources" =>  2,
                "influence" => 1,
                "trait" => "cultural",
                "legendary" => "I am legend",
                "specialties" => [
                    "propulsion",
                    "warfare"
                ]
            ],
            "expectedLegendary" => "I am legend",
            "expectedTraits" => [
                PlanetTrait::CULTURAL
            ],
            "expectedTechSpecialties" => [
                TechSpecialties::PROPULSION,
                TechSpecialties::WARFARE
            ]
        ];

        yield "A planet with legendary false" => [
            "jsonData" => [
                "name" => "Tinnes",
                "resources" =>  2,
                "influence" => 1,
                "trait" => "industrial",
                "legendary" => false,
                "specialties" => []
            ],
            "expectedLegendary" => null,
            "expectedTraits" => [
                PlanetTrait::INDUSTRIAL
            ],
            "expectedTechSpecialties" => []
        ];
        yield "A planet with multiple traits" => [
            "jsonData" => [
                "name" => "Tinnes",
                "resources" =>  2,
                "influence" => 1,
                "trait" => ["cultural", "hazardous"],
                "legendary" => null,
                "specialties" => []
            ],
            "expectedLegendary" => null,
            "expectedTraits" => [
                PlanetTrait::CULTURAL,
                PlanetTrait::HAZARDOUS
            ],
            "expectedTechSpecialties" => []
        ];
        yield "A planet with no traits" => [
            "jsonData" => [
                "name" => "Tinnes",
                "resources" =>  2,
                "influence" => 1,
                "trait" => null,
                "legendary" => null,
                "specialties" => []
            ],
            "expectedLegendary" => null,
            "expectedTraits" => [],
            "expectedTechSpecialties" => []
        ];
    }

    #[DataProvider("planetValues")]
    #[Test]
    public function itCalculatesOptimalValues(
        int $resources,
        int $influence,
        float $expectedOptimalResources,
        float $expectedOptimalInfluence
    ) {
        $planet = new Planet(
            $this->faker->word,
            $resources,
            $influence,
        );

        $this->assertSame($expectedOptimalResources, $planet->optimalResources);
        $this->assertSame($expectedOptimalInfluence, $planet->optimalInfluence);
        $this->assertSame($expectedOptimalInfluence + $expectedOptimalResources, $planet->optimalTotal);
    }

    #[DataProvider("jsonData")]
    #[Test]
    public function itcanCreateAPlanetFromJsonData(
        array $jsonData,
        ?string $expectedLegendary,
        array $expectedTraits,
        array $expectedTechSpecialties,
    ) {
        $planet = Planet::fromJsonData($jsonData);

        $this->assertSame($jsonData['name'], $planet->name);
        $this->assertSame($jsonData['resources'], $planet->resources);
        $this->assertSame($jsonData['influence'], $planet->influence);
        $this->assertSame($expectedTraits, $planet->traits);
        $this->assertSame($expectedTechSpecialties, $planet->specialties);
        $this->assertSame($expectedLegendary, $planet->legendary);
    }

    // @todo maybe move this to a JsonValidationTest
    #[Test]
    public function allPlanetsInJsonAreValid() {
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