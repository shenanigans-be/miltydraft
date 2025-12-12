<?php

namespace App\Game;

class Planet
{
    public float $optimalTotal;
    public float $optimalResources;
    public float $optimalInfluence;

    public function __construct(
        public string $name,
        public int $resources,
        public int $influence,
        public ?string $legendary = null,
        /**
         * @var array<PlanetTrait>
         */
        public array $traits = [],
        /**
         * @var array<TechSpecialties>
         */
        public array $specialties = []
    ) {
        $this->optimalResources = 0;
        $this->optimalInfluence = 0;

        if ($this->influence > $this->resources) {
            $this->optimalInfluence = $this->influence;
        } elseif ($this->resources > $this->influence) {
            $this->optimalResources = $this->resources;
        } elseif ($this->resources == $this->influence) {
            $this->optimalInfluence = $this->influence / 2;
            $this->optimalResources = $this->resources / 2;
        }

        $this->optimalTotal = $this->optimalResources + $this->optimalInfluence;
    }

    public static function fromJsonData(array $data): self
    {
        return new self(
            $data['name'],
            $data['resources'],
            $data['influence'],
            // @todo refactor tiles.json to use legendary: null instead of false
            ($data['legendary'] !== false) ? $data['legendary'] : null,
            self::traitsFromJsonData($data['trait']),
            self::techSpecialtiesFromJsonData($data['specialties'])
         );
    }

    // @todo update the tiles.json so that all planets just have an array of traits
    /**
     * @param string|array|null $data
     * @return array<PlanetTrait>
     */
    private static function traitsFromJsonData(string|array|null $data): array
    {
        if ($data == null) {
            return [];
        } else {
            // process array of traits, even if it's just one string
            return array_map(
                fn (string $str) => PlanetTrait::from($str),
                is_array($data) ? $data : [$data]
            );
        }
    }

    /**
     * @param array $data
     * @return array<TechSpecialties>
     */
    private static function techSpecialtiesFromJsonData(array $data): array
    {
        return array_map(
            fn (string $str) => TechSpecialties::from($str),
            $data
        );
    }
}
