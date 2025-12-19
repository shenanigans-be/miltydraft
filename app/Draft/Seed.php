<?php

namespace App\Draft;

class Seed
{
    // Maximum value for random seed generation (2^50)
    // Limited by JavaScript's Number.MAX_SAFE_INTEGER (2^53 - 1) for JSON compatibility
    public const MAX_VALUE = 1125899906842624;
    public const MIN_VALUE = 1;

    private const OFFSET_SLICES = 0;
    private const OFFSET_FACTIONS = 1;
    private const OFFSET_PLAYER_ORDER = 2;
    private int $seed;

    public function __construct(?int $seed = null)
    {
        if ($seed == null) {
            $this->seed = self::generate();
        } else {
            $this->seed = $seed;
        }
    }

    protected static function generate(): int
    {
        return mt_rand(1, self::MAX_VALUE);
    }

    public function getValue(): int
    {
        return $this->seed;
    }

    public function setForFactions()
    {
        mt_srand($this->seed + self::OFFSET_FACTIONS);
    }

    public function setForSlices($previousTries = 0)
    {
        mt_srand($this->seed + self::OFFSET_SLICES + $previousTries);
    }

    public function setForPlayerOrder()
    {
        mt_srand($this->seed + self::OFFSET_PLAYER_ORDER);
    }

    public function isValid()
    {
        return $this->seed >= self::MIN_VALUE && $this->seed <= self::MAX_VALUE;
    }
}