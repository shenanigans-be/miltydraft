<?php

declare(strict_types=1);

namespace App\TwilightImperium;

enum Wormhole: string
{
    case ALPHA = 'alpha';
    case BETA = 'beta';
    case GAMMA = 'gamma';
    case DELTA = 'delta';
    case EPSILON = 'epsilon';

    public function symbol(): string
    {
        return match($this) {
            self::ALPHA => '&alpha;',
            self::BETA => '&beta;',
            self::GAMMA => '&gamma;',
            self::DELTA => '&delta;',
            self::EPSILON => '&eplison;',
        };
    }

    /**
     * @todo refactor tiles.json to use arrays instead of a single string
     *
     * @param string $wormhole
     * @return array<Wormhole>
     */
    public static function fromJsonData(?string $wormhole): array
    {
        if ($wormhole == null) return [];
        if ($wormhole == 'alpha-beta') {
            return [
                self::ALPHA,
                self::BETA,
            ];
        } if ($wormhole == 'all') {
            // Mallice
            return [
                self::ALPHA,
                self::BETA,
                self::GAMMA,
            ];
        } else {
            return [
                self::from($wormhole),
            ];
        }
    }
}