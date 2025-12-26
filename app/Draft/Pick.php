<?php

declare(strict_types=1);

namespace App\Draft;

class Pick
{
    public function __construct(
        public readonly PlayerId $playerId,
        public readonly PickCategory $category,
        public readonly string $pickedOption,
    ) {
    }

    public static function fromJson(array $data): self
    {
        return new self(
            PlayerId::fromString($data['player']),
            PickCategory::from($data['category']),
            $data['value'],
        );
    }

    public function toArray(): array
    {
        return [
            'player' => $this->playerId->value,
            'category' => $this->category->value,
            'value' => $this->pickedOption,
        ];
    }
}