<?php

namespace App\TwilightImperium;

use App\Testing\FakesData;
use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class EntityWithResourcesAndInfluenceTest extends TestCase
{
    use FakesData;

    public static function values(): iterable
    {
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

    #[DataProvider("values")]
    #[Test]
    public function itCalculatesOptimalValues(
        int   $resources,
        int   $influence,
        float $expectedOptimalResources,
        float $expectedOptimalInfluence
    )
    {
        $planet = new Planet(
            $this->faker->word,
            $resources,
            $influence,
        );

        $this->assertSame($expectedOptimalResources, $planet->optimalResources);
        $this->assertSame($expectedOptimalInfluence, $planet->optimalInfluence);
        $this->assertSame($expectedOptimalInfluence + $expectedOptimalResources, $planet->optimalTotal);
    }
}