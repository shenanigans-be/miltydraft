<?php

namespace App\TwilightImperium;

class EntityWithResourcesAndInfluence
{
    public float $optimalTotal = 0;
    public float $optimalResources = 0;
    public float $optimalInfluence = 0;

    public function __construct(
        public int $resources,
        public int $influence
    )
    {
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
}