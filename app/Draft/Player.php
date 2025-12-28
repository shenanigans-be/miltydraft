<?php

declare(strict_types=1);

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
        public readonly ?string $team = null,
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
            null,
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
            $team,
        );
    }

    public function unclaim(): Player
    {
        if (! $this->claimed) {
            throw InvalidClaimException::playerNotClaimed();
        }

        return new self(
            $this->id,
            $this->name,
            false,
            $this->pickedPosition,
            $this->pickedFaction,
            $this->pickedSlice,
            $this->team,
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
            $this->team,
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

    public function getPick(PickCategory $category): ?string
    {
        return match($category) {
            PickCategory::POSITION => $this->pickedPosition,
            PickCategory::SLICE => $this->pickedSlice,
            PickCategory::FACTION => $this->pickedFaction,
        };
    }

    public function hasPicked(PickCategory $category): bool
    {
        return match($category) {
            PickCategory::FACTION => $this->hasPickedFaction(),
            PickCategory::SLICE => $this->hasPickedSlice(),
            PickCategory::POSITION => $this->hasPickedPosition(),
        };
    }

    public function pick(Pick $pick): Player
    {
        if ($this->hasPicked($pick->category)) {
            throw InvalidPickException::playerHasAlreadyPicked($pick->category);
        }

        return new self(
            $this->id,
            $this->name,
            $this->claimed,
            $pick->category == PickCategory::POSITION ? $pick->pickedOption : $this->pickedPosition,
            $pick->category == PickCategory::FACTION ? $pick->pickedOption : $this->pickedFaction,
            $pick->category == PickCategory::SLICE ? $pick->pickedOption : $this->pickedSlice,
            $this->team,
        );
    }

    public function unpick(PickCategory $category): Player
    {
        if (! $this->hasPicked($category)) {
            throw InvalidPickException::cannotUnpick($category);
        }

        return new self(
            $this->id,
            $this->name,
            $this->claimed,
            $category == PickCategory::POSITION ? null : $this->pickedPosition,
            $category == PickCategory::FACTION ? null : $this->pickedFaction,
            $category == PickCategory::SLICE ? null : $this->pickedSlice,
            $this->team,
        );
    }
}