<?php

namespace App\Draft;

class Player
{
    public function __construct(
        public string $id,
        public string $name,
        public bool $claimed = false,
        // enum with the 8 positions?
        public ?string $pickedPosition = null,
        public ?string $pickedFaction = null,
        public ?string $pickedSlice = null,
        public ?string $team = null
    ) {
    }

    public static function fromJson($playerData): self
    {
        return new self(
            $playerData['id'],
            $playerData['name'],
            $playerData['claimed'],
            $playerData['position'],
            $playerData['faction'],
            $playerData['slice'],
            $playerData['team'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'claimed' => $this->claimed,
            'position' => $this->pickedPosition,
            'faction' => $this->pickedFaction,
            'slice' => $this->pickedSlice,
            'team' => $this->team,
        ];
    }

    public function hasPickedSlice(): bool
    {
        return $this->pickedSlice != null;
    }

    public function hasPickedFaction(): bool
    {
        return $this->pickedFaction != null;
    }

    public function hasPickedPosition(): bool
    {
        return $this->pickedPosition != null;
    }
}