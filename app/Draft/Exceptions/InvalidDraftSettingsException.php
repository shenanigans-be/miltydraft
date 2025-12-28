<?php

declare(strict_types=1);

namespace App\Draft\Exceptions;

use App\Draft\Seed;

class InvalidDraftSettingsException extends \Exception
{

    public static function notAllPlayerNamesAreFilled(): self
    {
        return new self('Not all player names are filled in');
    }

    public static function playerNamesNotUnique(): self
    {
        return new self('Player names are not unique');
    }

    public static function notEnoughPlayers(): self
    {
        return new self('Should have at least 3 players');
    }

    public static function notEnoughSlicesForPlayers(): self
    {
        return new self('Cannot have less slices than players');
    }

    public static function notEnoughFactionsForPlayers(): self
    {
        return new self('Cannot have less factions than players');
    }

    public static function notEnoughTilesForSlices(float $maxSlices): self
    {
        return new self(sprintf('This selection of tilesets only supports %d slices', $maxSlices));
    }

    public static function notEnoughSlicesForLegendaryPlanets(): self
    {
        return new self('Cannot have more legendary planets than slices');
    }

    public static function notEnoughLegendaryPlanets(int $max): self
    {
        return new self(sprintf('This selection of tilesets only supports %d legendary planets', $max));
    }

    public static function invalidMaximumOptimal(): self {
        return new self("Maximum optimal can't be less than minimum");
    }

    public static function invalidSeed(): self {
        return new self(sprintf('Seed must be between %d and %d', Seed::MIN_VALUE, Seed::MAX_VALUE));
    }

    public static function notEnoughFactionsInSet(int $max): self {
        return new self(sprintf('These faction sets only support up to %d factions', $max));
    }

    public static function notEnoughCustomSlices(): self {
        return new self('Not enough custom slices for player count');
    }

    public static function invalidCustomSlices(): self {
        return new self("Custom slices error, either the formatting is incorrect or slices don't have enough tiles (each should have 5)");
    }

    public static function cannotGenerateSlices(): self {
        return new self('Selection contains no valid slices. This happens occasionally to valid configurations but it probably means that the parameters are impossible.');
    }
}