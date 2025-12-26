<?php

declare(strict_types=1);

namespace App\Draft;

class TilePool
{
    public function __construct(
        /** @var array<string> $highTier */
        public array $highTier,
        /** @var array<string> $midTier */
        public array $midTier,
        /** @var array<string> $lowTier */
        public array $lowTier,
        /** @var array<string> $redTier */
        public array $redTier,
    ) {
    }

    public function shuffle(): void
    {
        shuffle($this->highTier);
        shuffle($this->midTier);
        shuffle($this->lowTier);
        shuffle($this->redTier);
    }

    public function slice(int $numberOfSlices): TilePool
    {
        return new TilePool(
            array_slice($this->highTier, 0, $numberOfSlices),
            array_slice($this->midTier, 0, $numberOfSlices),
            array_slice($this->lowTier, 0, $numberOfSlices),
            array_slice($this->redTier, 0, $numberOfSlices * 2),
        );
    }

    /**
     * @return array<string>
     */
    public function allIds(): array
    {
        return array_merge($this->highTier, $this->midTier, $this->lowTier, $this->redTier);
    }

    public function slices(): array
    {

    }
}