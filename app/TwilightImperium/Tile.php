<?php


namespace App\TwilightImperium;

class Tile
{
    public int $totalInfluence = 0;
    public int $totalResources = 0;
    public float $optimalInfluence = 0;
    public float $optimalResources = 0;
    public float $optimalTotal = 0;

    public function __construct(
        public string $id,
        public TileType $tileType,
        public TileTier $tier,
        public Edition $edition,
        /**
         * @var array<Planet>
         */
        public array $planets = [],
        /**
         * @var array<SpaceStation>
         */
        public array $spaceStations = [],
        /**
         * @var array<Wormhole>
         */
        public array $wormholes = [],
        // @todo anomaly enum, but not priority because it doesn't influence generator
        public ?string $anomaly = null,
        // @todo make a Hyperlane class, but not priority because  it doesn't influence generator
        public array $hyperlanes = [],
    ) {
        // calculate total and optimal values
        foreach (array_merge($this->planets, $this->spaceStations) as $entity) {
            $this->totalInfluence += $entity->influence;
            $this->totalResources += $entity->resources;
            $this->optimalResources += $entity->optimalResources;
            $this->optimalInfluence += $entity->optimalInfluence;
            $this->optimalTotal += $entity->optimalTotal;
        }
    }

    public static function fromJsonData(
        string $id,
        TileTier $tier,
        array $data
    ): self {
        return new self(
            $id,
            TileType::from($data['type']),
            $tier,
            Edition::from($data['set']),
            array_map(fn(array $planetData) => Planet::fromJsonData($planetData), $data['planets'] ?? []),
            array_map(fn(array $stationData) => SpaceStation::fromJsonData($stationData), $data['stations'] ?? []),
            Wormhole::fromJsonData($data['wormhole']),
            $data['anomaly'] ?? null,
            isset($data['hyperlanes']) ? $data['hyperlanes'] : [],
        );
    }

    function hasAnomaly()
    {
        return $this->anomaly != null;
    }

    function hasWormhole($wormhole)
    {
        return in_array($wormhole, $this->wormholes);
    }

    function hasLegendaryPlanet()
    {
        foreach ($this->planets as $p) {
            if ($p->isLegendary()) return true;
        }

        return false;
    }


    /**
     * @todo deprecate
     *
     * @param Tile[] $tiles
     * @return int[]
     */
    public static function countSpecials(array $tiles)
    {
        $count = [
            "legendary" => 0
        ];
        foreach(Wormhole::cases() as $wormhole) {
            $count[$wormhole->value] = 0;
        }

        foreach ($tiles as $tile) {
            foreach ($tile->wormholes as $w) [
                $count[$w->value]++
            ];

            if ($tile->hasLegendaryPlanet()) $count["legendary"]++;
        }

        return $count;
    }

    /**
     * @return string, TileTier
     */
    public static function tierData(): array
    {
        $tierData = json_decode(file_get_contents('data/tile-selection.json'));
        $tileTiers = [];

        foreach($tierData as $tierLists) {
            foreach($tierLists as $level => $list) {
                foreach($list as $id) {
                    $tileTiers[$id] = TileTier::from($level);
                }
            }
        }

        return $tileTiers;
    }

    /**
     * @return array<string, Tile>
     */
    public static function all(): array
    {
        $allTileData = json_decode(file_get_contents('data/tiles.json'), true);
        $tileTiers = self::tierData();
        $tiles = [];

        // merge tier and tile data
        // We're keeping it in separate files for maintainability
        foreach ($allTileData as $tileId => $tileData) {
            $isMecRexOrMallice = count($tileData['planets']) > 0 &&
                ($tileData['planets'][0]['name'] == "Mecatol Rex" || $tileData['planets'][0]['name'] == "Mallice");

            $tier = match($tileData['type']) {
                "red" => TileTier::RED,
                "blue" => $isMecRexOrMallice ? TileTier::NONE : $tileTiers[$tileId],
                default => TileTier::NONE
            };

            $tiles[$tileId] = Tile::fromJsonData($tileId, $tier, $tileData);
        }

        return $tiles;
    }
}
