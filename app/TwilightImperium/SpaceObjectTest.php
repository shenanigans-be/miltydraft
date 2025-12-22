<?php

namespace App\TwilightImperium;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class SpaceObjectTest extends TestCase
{
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
        $entity = new SpaceObject(
            $resources,
            $influence
        );

        $this->assertSame($expectedOptimalResources, $entity->optimalResources);
        $this->assertSame($expectedOptimalInfluence, $entity->optimalInfluence);
        $this->assertSame($expectedOptimalInfluence + $expectedOptimalResources, $entity->optimalTotal);
    }
}