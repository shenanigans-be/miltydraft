<?php

namespace App\Draft;

class Draft
{
    public function __construct(
        public string $id,
        public bool $isDone,
        /**
         * @var array<Player> $players
         */
        public array $players,
        public DraftSettings $settings,
        // @todo secrets
        // @todo logs
    ) {

    }

    public static function fromJson($data)
    {
        return new self(
            $data['id'],
            $data['done'],
            array_map(fn ($playerData) => Player::fromJson($playerData), $data['draft']['players']),
            DraftSettings::fromJson($data['config'])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'done' => $this->isDone,
            'config' => $this->settings->toArray(),
            'players' => array_map(fn (Player $player) => $player->toArray(), $this->players),
        ];
    }
}