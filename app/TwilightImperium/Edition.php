<?php

declare(strict_types=1);

namespace App\TwilightImperium;

enum Edition: string
{

    case BASE_GAME = 'BaseGame';
    case PROPHECY_OF_KINGS = 'PoK';
    case THUNDERS_EDGE = 'TE';
    case DISCORDANT_STARS = 'DS';
    // @todo merge DS and DS plus?
    case DISCORDANT_STARS_PLUS = 'DSPlus';

    public function fullName(): string
    {
        return match($this) {
            Edition::BASE_GAME => 'Base Game',
            Edition::PROPHECY_OF_KINGS => 'Prophecy of Kings',
            Edition::THUNDERS_EDGE => "Thunder's Edge",
            Edition::DISCORDANT_STARS => 'Discordant Stars',
            Edition::DISCORDANT_STARS_PLUS => 'Discordant Stars Plus',
        };
    }

    /**
     * For now, only discordant stars doesn't have a dedicated tileset, but that might change
     *
     * @return array<self>
     */
    private static function editionsWithoutTiles(): array
    {
        return [
            self::DISCORDANT_STARS,
        ];
    }

    public function hasValidTileSet(): bool
    {
        return ! in_array($this, self::editionsWithoutTiles());
    }

    // @todo move to tileset class?
    public function blueTileCount(): int
    {
        return match($this) {
            Edition::BASE_GAME => 20,
            Edition::PROPHECY_OF_KINGS => 16,
            Edition::THUNDERS_EDGE => 15,
            Edition::DISCORDANT_STARS => 0,
            Edition::DISCORDANT_STARS_PLUS => 16,
        };
    }

    public function redTileCount(): int
    {
        return match($this) {
            Edition::BASE_GAME => 12,
            Edition::PROPHECY_OF_KINGS => 6,
            Edition::THUNDERS_EDGE => 5,
            Edition::DISCORDANT_STARS => 0,
            Edition::DISCORDANT_STARS_PLUS => 8,
        };
    }

    public function legendaryPlanetCount(): int
    {
        return match($this) {
            Edition::BASE_GAME => 0,
            Edition::PROPHECY_OF_KINGS => 2,
            Edition::THUNDERS_EDGE => 5,
            Edition::DISCORDANT_STARS => 0,
            Edition::DISCORDANT_STARS_PLUS => 5,
        };
    }

    public function factionCount(): int
    {
        return match($this) {
            Edition::BASE_GAME => 17,
            Edition::PROPHECY_OF_KINGS => 7,
            Edition::THUNDERS_EDGE => 5,
            Edition::DISCORDANT_STARS => 24,
            Edition::DISCORDANT_STARS_PLUS => 10,
        };
    }
}