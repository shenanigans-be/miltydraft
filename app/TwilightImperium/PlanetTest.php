<?php

namespace App\TwilightImperium;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class PlanetTest extends TestCase
{

    public static function planets(): iterable
    {
        yield "For a legendary planet" => [
            'planet' => new Planet(
                "Legendplanet",
                0,
                0,
                "Some string value"
            ),
            'expected' => true
        ];
        yield "For a regular planet" => [
            'planet' => new Planet(
                "RegularJoePlanet",
                0,
                0,
                null
            ),
            'expected' => false
        ];
    }

    public static function jsonData(): iterable {
        $baseJsonData = [
            "name" => "Tinnes",
            "resources" =>  2,
            "influence" => 1,
        ];

        yield "A planet without a legendary" => [
            "jsonData" => $baseJsonData + [
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
            "jsonData" => $baseJsonData + [
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
            "jsonData" => $baseJsonData + [
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
            "jsonData" => $baseJsonData + [
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
            "jsonData" => $baseJsonData + [
                "trait" => null,
                "legendary" => null,
                "specialties" => []
            ],
            "expectedLegendary" => null,
            "expectedTraits" => [],
            "expectedTechSpecialties" => []
        ];
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

    #[DataProvider("planets")]
    #[Test]
    public function itExposesHasLegendaryMethod(Planet $planet, bool $expected) {
        $this->assertSame($expected, $planet->isLegendary());
    }
}