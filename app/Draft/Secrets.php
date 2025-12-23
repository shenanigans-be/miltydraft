<?php

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
      public array $playerSecrets = []
    ) {
    }

    public function toArray(): array
    {
        return [
            self::ADMIN_SECRET_KEY => $this->adminSecret,
            ...$this->playerSecrets
        ];
    }

    public static function generatePassword(): string
    {
        return base64_encode(random_bytes(16));
    }

    public function checkAdminSecret($secret): bool {
        return $secret == $this->adminSecret;
    }

    public function checkPlayerSecret($id, $secret): bool {
        return isset($this->playerSecrets[$id]) && $secret == $this->playerSecrets[$id];
    }

    public static function fromJson($data): self
    {
        return new self(
            $data[self::ADMIN_SECRET_KEY],
            array_filter($data, fn (string $key) => $key != self::ADMIN_SECRET_KEY, ARRAY_FILTER_USE_KEY)
        );
    }
}