<?php

namespace App\TwilightImperium;

class SpaceStation extends EntityWithResourcesAndInfluence
{
    public function __construct(
        public string $name,
        int $resources,
        int $influence
    )
    {
        parent::__construct($resources, $influence);
    }

    public static function fromJsonData(array $data): self
    {
        return new self(
            $data['name'],
            $data['resources'],
            $data['influence']
        );
    }
}
