<?php

declare(strict_types=1);

namespace App\TwilightImperium;

class SpaceStation extends SpaceObject
{
    public function __construct(
        public string $name,
        int $resources,
        int $influence,
    )
    {
        parent::__construct($resources, $influence);
    }

    public static function fromJsonData(array $data): self
    {
        return new self(
            $data['name'],
            $data['resources'],
            $data['influence'],
        );
    }
}
