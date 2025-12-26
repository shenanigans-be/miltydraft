<?php

namespace App\Draft;

use App\Draft\Exceptions\InvalidClaimException;
use App\Draft\Exceptions\InvalidPickException;

class Player
{
    public function __construct(
        public readonly PlayerId $id,
        public readonly string $name,
        public readonly bool $claimed = false,
        // enum with the 8 positions?
        public readonly ?string $pickedPosition = null,
        public readonly ?string $pickedFaction = null,
        public readonly ?string $pickedSlice = null,
        public readonly ?string $team = null
    ) {
    }

    public static function fromJson($playerData): self
    {
        return new self(
            PlayerId::fromString($playerData['id']),
            $playerData['name'],
            $playerData['claimed'],
            $playerData['position'],
            $playerData['faction'],
            $playerData['slice'],
            $playerData['team'] ?? null,
        );
    }

    public static function create(string $name)
    {
        return new self(
            PlayerId::generate(),
            $name,
            false,
            null,
            null,
            null
        );
    }

    public function putInTeam(string $team): Player
    {
        return new self(
            $this->id,
            $this->name,
            $this->claimed,
            $this->pickedPosition,
            $this->pickedFaction,
            $this->pickedSlice,
            $team
        );
    }

    public function unclaim(): Player
    {
        if (!$this->claimed) {
            throw InvalidClaimException::playerNotClaimed();
        }

        return new self(
            $this->id,
            $this->name,
            false,
            $this->pickedPosition,
            $this->pickedFaction,
            $this->pickedSlice,
            $this->team
        );
    }

    public function claim(): Player
    {
        if ($this->claimed) {
            throw InvalidClaimException::playerAlreadyClaimed();
        }

        return new self(
            $this->id,
            $this->name,
            true,
            $this->pickedPosition,
            $this->pickedFaction,
            $this->pickedSlice,
            $this->team
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value,
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

    public function hasPicked(PickCategory $category): bool
    {
        return match($category) {
            PickCategory::FACTION => $this->hasPickedFaction(),
            PickCategory::SLICE => $this->hasPickedSlice(),
            PickCategory::POSITION => $this->hasPickedPosition(),
        };
    }

    public function pick(PickCategory $category, string $pick): Player
    {
        if ($this->hasPicked($category)) {
            throw InvalidPickException::playerHasAlreadyPicked($category);
        }

        return new self(
            $this->id,
            $this->name,
            $this->claimed,
            $category == PickCategory::POSITION ? $pick : $this->pickedPosition,
            $category == PickCategory::FACTION ? $pick : $this->pickedFaction,
            $category == PickCategory::SLICE ? $pick : $this->pickedSlice,
            $this->team,
        );
    }
}