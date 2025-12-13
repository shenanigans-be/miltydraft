<?php

namespace App\TwilightImperium;

class Planet extends EntityWithResourcesAndInfluence
{
    public function __construct(
        public string $name,
        int $resources,
        int $influence,
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
        parent::__construct($resources, $influence);
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

    public function isLegendary(): bool {
        return $this->legendary != null;
    }
}
