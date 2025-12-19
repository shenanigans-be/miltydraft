<?php

namespace App\Draft;

class Draft
{
    public function __construct(
        public string   $id,
        public bool     $isDone,
        /**
         * @var array<string, Player> $players
         */
        public array    $players,
        public Settings $settings,
        public Secrets  $secrets,
        /**
         * @var array<Pick> $log
         */
        public array $log = [],
        public ?PlayerId $currentPlayerId = null,
        // @todo Current
        // @todo Slices
        // @todo Factions
    ) {
    }

    public static function fromJson($data)
    {
        /**
         * @var array<string, Player>
         */
        $players = array_reduce($data['draft']['players'], function ($players, $playerData) {
            $player = Player::fromJson($playerData);
            $players[$player->id->value] = $player;
            return $players;
        }, []);

        return new self(
            $data['id'],
            $data['done'],
            $players,
            Settings::fromJson($data['config']),
            Secrets::fromJson($data['secrets']),
            array_map(fn ($logData) => Pick::fromJson($logData), $data['draft']['log']),
            PlayerId::fromString($data['draft']['current'])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'done' => $this->isDone,
            'config' => $this->settings->toArray(),
            'draft' => [
                'players' => array_map(fn (Player $player) => $player->toArray(), $this->players),
                'log' =>  array_map(fn (Pick $pick) => $pick->toArray(), $this->log),
                'current' => $this->currentPlayerId->value
            ],
            'secrets' => $this->secrets->toArray(),
        ];
    }
}