<?php

declare(strict_types=1);

namespace App\Testing;

use App\TwilightImperium\Edition;

class TestSets
{

    /**
     * Some combination of sets that players might select
     */
    public static function setCombinations(): \Generator
    {
        yield 'For base game only' => [
            'sets' => [Edition::BASE_GAME],
        ];

        yield 'For base game + Discordant' => [
            'sets' => [Edition::BASE_GAME, Edition::DISCORDANT_STARS, Edition::DISCORDANT_STARS_PLUS],
        ];

        yield 'For base game + POK' => [
            'sets' => [Edition::BASE_GAME, Edition::PROPHECY_OF_KINGS],
        ];

        yield "For base game + Thunder's edge" => [
            'sets' => [Edition::BASE_GAME, Edition::THUNDERS_EDGE],
        ];

        yield 'For all official editions' => [
            'sets' => [Edition::BASE_GAME, Edition::PROPHECY_OF_KINGS, Edition::THUNDERS_EDGE],
        ];

        yield 'For base game + POK + Discordant' => [
            'sets' => [Edition::BASE_GAME, Edition::PROPHECY_OF_KINGS, Edition::DISCORDANT_STARS, Edition::DISCORDANT_STARS_PLUS],
        ];

        yield 'For the whole shebang' => [
            'sets' => [
                Edition::BASE_GAME,
                Edition::PROPHECY_OF_KINGS,
                Edition::THUNDERS_EDGE,
                Edition::DISCORDANT_STARS,
                Edition::DISCORDANT_STARS_PLUS,
            ],
        ];
    }
}