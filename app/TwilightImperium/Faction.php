<?php

namespace App\TwilightImperium;

/**
 * @todo implement a Faction enum and have arrays of enum values
 * based on editions (other data can still be fetched from the json)
 */
class Faction
{
    /**
     * @var array<string, Faction> $allFactionData
     */
    private static array $allFactionData;

    public function __construct(
        public readonly string $name,
        public readonly string $id,
        public readonly string $homeSystemTileNumber,
        public readonly string $linkToWiki,
        public readonly Edition $edition,
    ) {
    }

    public static function fromJson($data)
    {
        return new self(
            $data['name'],
            $data['id'],
            $data['homesystem'],
            $data['wiki'],
            self::editionFromFactionJson($data['set']),
        );
    }

    /**
     * @return array<string, Faction>
     */
    public static function all(): array
    {
        $rawData = json_decode(file_get_contents('data/factions.json'), true);
        return array_map(fn ($factionData) => self::fromJson($factionData), $rawData);
    }

    //
    private static function editionFromFactionJson($factionEdition): Edition
    {
        return match ($factionEdition) {
            "base" => Edition::BASE_GAME,
            "pok" => Edition::PROPHECY_OF_KINGS,
            "te" => Edition::THUNDERS_EDGE,
            "keleres" => Edition::THUNDERS_EDGE,
            "discordant" => Edition::DISCORDANT_STARS,
            "discordantexp" => Edition::DISCORDANT_STARS_PLUS,
            default => throw new \Exception("Faction has invalid set")
        };
    }

    /**
     * @todo fix this mess
     */
    public function homesystem(): string
    {
        if(in_array($this->edition, [Edition::DISCORDANT_STARS, Edition::DISCORDANT_STARS_PLUS])) {
            return 'DS_' . $this->id;
        } else {
            return $this->homeSystemTileNumber;
        }
    }
}