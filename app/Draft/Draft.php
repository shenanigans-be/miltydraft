<?php

namespace App\Draft;

use App\Draft\Generators\FactionPoolGenerator;
use App\Draft\Generators\SlicePoolGenerator;

class Draft
{
    public function __construct(
        public string   $id,
        public bool     $isDone,
        /** @var array<string, Player> $players */
        public array    $players,
        public Settings $settings,
        public Secrets  $secrets,
        /** @var array<Slice> $slices */
        public array $slices,
        /** @var array<string> $factionPool */
        public array $factionPool,
        /** @var array<Pick> $log */
        public array $log = [],
        public ?PlayerId $currentPlayerId = null,
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
            [],
            $data['factions'],
            array_map(fn ($logData) => Pick::fromJson($logData), $data['draft']['log']),
            PlayerId::fromString($data['draft']['current'])
        );
    }

    public function toFileContent(): string
    {
        return json_encode($this->toArray(true));
    }

    public function toArray($includeSecrets = false): array
    {
        $data = [
            'id' => $this->id,
            'done' => $this->isDone,
            'config' => $this->settings->toArray(),
            'draft' => [
                'players' => array_map(fn (Player $player) => $player->toArray(), $this->players),
                'log' =>  array_map(fn (Pick $pick) => $pick->toArray(), $this->log),
                'current' => $this->currentPlayerId->value
            ],
            'factions' => $this->factionPool
        ];

        if ($includeSecrets) {
            $data['secrets'] = $this->secrets->toArray();
        }

        return $data;
    }

    public static function createFromSettings(Settings $settings)
    {
        $factionPooLGenerator = new FactionPoolGenerator($settings);
        $slicePoolGenerator = new SlicePoolGenerator($settings);

        return new self(
            DraftId::generate(),
            false,
            // @todo
            [],
            $settings,
            Secrets::new(),
            $slicePoolGenerator->generate(),
            $factionPooLGenerator->generate(),
            [],
            // @todo
            null
        );
    }


}