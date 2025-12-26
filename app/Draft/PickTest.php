<?php

declare(strict_types=1);

namespace App\Draft;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class PickTest extends TestCase
{
    public static function pickCases(): iterable
    {
        yield 'For a faction pick' => [
            'pickData' => [
                'player' => '1234',
                'category' => 'faction',
                'value' => 'Xxcha',
            ],
        ];

        yield 'For a position pick' => [
            'pickData' => [
                'player' => '1234',
                'category' => 'position',
                'value' => '4',
            ],
        ];

        yield 'For a slice pick' => [
            'pickData' => [
                'player' => '1234',
                'category' => 'slice',
                'value' => '1',
            ],
        ];
    }

    #[Test]
    #[DataProvider('pickCases')]
    public function itCanConvertFromJsonData($pickData): void
    {
        $pick = Pick::fromJson($pickData);

        $this->assertSame($pick->pickedOption, $pickData['value']);
        $this->assertSame($pick->category->value, $pickData['category']);
        $this->assertSame($pick->playerId->value, $pickData['player']);
    }
}