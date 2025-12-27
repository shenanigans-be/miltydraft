<?php

declare(strict_types=1);

namespace App\Draft;

/**
 * Contains and generates the player "passwords" (used to authenticate across devices)
 */
class Secrets
{
    private const ADMIN_SECRET_KEY = 'admin_pass';

    public function __construct(
        public readonly string $adminSecret,
        /**
         * @var array<string, string> $playerSecrets
         */
      public array $playerSecrets = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            self::ADMIN_SECRET_KEY => $this->adminSecret,
            ...$this->playerSecrets,
        ];
    }

    public static function generateSecret(): string
    {
        return bin2hex(random_bytes(16));
    }

    public function generateSecretForPlayer(PlayerId $playerId): string
    {
        $secret = self::generateSecret();
        $this->playerSecrets[$playerId->value] = $secret;

        return $secret;
    }

    public function removeSecretForPlayer(PlayerId $playerId): void
    {
        unset($this->playerSecrets[$playerId->value]);
    }

    public function secretById(PlayerId $playerId): ?string
    {
        return $this->playerSecrets[$playerId->value] ?? null;
    }

    public function checkAdminSecret(?string $secret): bool {
        if ($secret == null) {
            return false;
        }

        return $secret == $this->adminSecret;
    }

    public function playerIdBySecret(string $secret): ?PlayerId {
        foreach($this->playerSecrets as $key => $playerSecret) {
            if ($playerSecret == $secret) {
                return PlayerId::fromString($key);
            }
        }

        return null;
    }

    public function checkPlayerSecret(PlayerId $id, ?string $secret): bool {
        if ($secret == null) {
            return false;
        }

        return isset($this->playerSecrets[$id->value]) && $secret == $this->playerSecrets[$id->value];
    }

    public static function fromJson($data): self
    {
        return new self(
            $data[self::ADMIN_SECRET_KEY],
            array_filter($data, fn (string $key) => $key != self::ADMIN_SECRET_KEY, ARRAY_FILTER_USE_KEY),
        );
    }
}