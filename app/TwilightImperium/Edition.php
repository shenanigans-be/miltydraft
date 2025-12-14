<?php

namespace App\TwilightImperium;

enum Edition: string
{

    case BASE_GAME = "BaseGame";
    case PROPHECY_OF_KINGS = "PoK";
    case THUNDERS_EDGE = "TE";
    case CODEX_IV = "CodexIV";
    case DISCORDANT_STARS = "DS";
    // @todo merge DS and DS plus?
    case DISCORDANT_STARS_PLUS = "DSPlus";

    /**
     * For now, only discordant stars doesn't have a dedicated tileset, but that might change
     *
     * @return array<self>
     */
    private static function editionsWithoutTiles(): array
    {
        return [
            self::DISCORDANT_STARS
        ];
    }

    public static function hasValidTileSet(Edition $edition): bool
    {
        return !in_array($edition, self::editionsWithoutTiles());
    }
}