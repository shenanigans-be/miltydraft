<?php

namespace App;

class GeneratorConfig
{
    public $players = [];
    public $name;
    public $num_players;
    public $num_slices;
    public $num_factions;
    public $include_pok;
    public $include_ds_tiles;
    public $include_base_factions;
    public $include_pok_factions;
    public $include_keleres;
    public $include_discordant;
    public $include_discordantexp;

    public $min_wormholes;
    public $min_legendaries;
    public $max_1_wormhole;

    public $minimum_optimal_influence;
    public $minimum_optimal_resources;
    public $minimum_optimal_total;
    public $maximum_optimal_total;

    public $custom_factions = null;
    public $custom_slices = null;

    function __construct($get_values_from_request)
    {
        if ($get_values_from_request) {
            $names = get('player', []);
            shuffle($names);
            foreach ($names as $name) {
                if ($name != '')  $this->players[] = htmlentities($name);
            }

            $this->name = get('game_name', '');
            if (trim($this->name) == '') $this->name = $this->generateName();
            else $this->name = htmlentities($this->name);
            $this->num_players = (int) get('num_players');
            $this->num_slices = (int) get('num_slices');
            $this->num_factions = (int) get('num_factions');
            $this->include_pok = get('include_pok') == true;
            $this->include_ds_tiles = get('include_ds_tiles') == true;
            $this->include_base_factions = get('include_base_factions') == true;
            $this->include_pok_factions = get('include_pok_factions') == true;
            $this->include_keleres = get('include_keleres') == true;
            $this->include_discordant = get('include_discordant') == true;
            $this->include_discordantexp = get('include_discordantexp') == true;

            $this->max_1_wormhole = get('max_wormhole') == true;
            $this->min_wormholes = (get('wormholes') == true) ? 2 : 0;
            $this->min_legendaries = (int) get('legendary');

            $this->minimum_optimal_influence = (float) get('min_inf');
            $this->minimum_optimal_resources = (float) get('min_res');
            $this->minimum_optimal_total = (float) get('min_total');
            $this->maximum_optimal_total = (float) get('max_total');

            if (!empty(get('custom_factions', []))) {
                $this->custom_factions = get('custom_factions');
            }

            if (get('custom_slices') != '') {
                $slice_data = explode("\n", get('custom_slices'));
                $this->custom_slices = [];
                foreach ($slice_data as $s) {
                    $slice = [];
                    $t = explode(',', $s);
                    foreach ($t as $tile) {
                        $tile = trim($tile);
                        $slice[] = $tile;
                    }
                    $this->custom_slices[] = $slice;
                }
            }

            $this->validate();
        }
    }


    public static function fromDraft($draft): GeneratorConfig
    {
        $config = new GeneratorConfig(false);

        foreach (array_keys(get_object_vars($config)) as $key) {
            if (isset($draft->getConfig()[$key])) {
                $config->$key = $draft->getConfig()[$key];
            }
        }
        $config->num_players = count($config->players);

        $config->validate();

        return $config;
    }

    private function validate(): void
    {

        if (count($this->players) < $this->num_players) return_error('Some players names are not filled out');
        if (!$this->include_pok && $this->num_slices > 5) return_error('Can only draft up to 5 slices without PoK. (And by extension you can only do drafts up to 5 players)');
        if (!$this->include_ds_tiles && $this->num_slices > 9) return_error('Can only draft up to 9 slices without DS+ Tiles.');
        if ($this->num_players < 3) return_error('Please enter more than 3 players');
        if ($this->num_factions < $this->num_players) return_error("Can't have less factions than players");
        if ($this->num_slices < $this->num_players) return_error("Can't have less slices than players");
        if ($this->maximum_optimal_total < $this->minimum_optimal_total) return_error("Maximum optimal can't be less than minimum");
        // Must include at least 1 of base, pok, discordant, or discordant expansion to have enough factions to use
        if (!($this->include_base_factions || $this->include_pok_factions || $this->include_discordant || $this->include_discordantexp)) return_error("Not enough factions selected.");
        // if($this->custom_factions != null && count($this->custom_factions) < $this->num_players) return_error("Not enough custom factions for number of players");
        if ($this->custom_slices != null) {
            if (count($this->custom_slices) < $this->num_players) return_error("Not enough custom slices for number of players");
            foreach ($this->custom_slices as $s) {
                if (count($s) != 5) return_error('Some of the custom slices have the wrong number of tiles. (each should have five)');
            }
        }
    }

    private function generateName(): string
    {
        $adjectives = [
            'adventurous', 'aggressive', 'angry', 'arrogant', 'beautiful', 'bloody', 'blushing', 'brave',
            'clever', 'clumsy', 'combative', 'confused', 'crazy', 'curious', 'defiant', 'difficult', 'disgusted', 'doubtful', 'easy',
            'famous',  'fantastic', 'filthy', 'frightened', 'funny', 'glamorous', 'gleaming', 'glorious',
            'grumpy', 'homeless', 'hilarious', 'impossible', 'itchy', 'imperial', 'jealous', 'long', 'magnificent', 'lucky',
            'modern', 'mysterious', 'naughty', 'old-fashioned', 'outstanding', 'outrageous', 'perfect',
            'poisoned', 'puzzled', 'rich', 'smiling', 'super', 'tasty', 'terrible', 'wandering', 'zealous'
        ];
        $nouns = [
            'people', 'history', 'art', 'world', 'space', 'universe', 'galaxy', 'story',
            'map', 'game', 'family', 'government', 'system', 'method', 'computer', 'problem',
            'theory', 'law', 'power', 'knowledge', 'control', 'ability', 'love', 'science',
            'fact', 'idea', 'area', 'society', 'industry', 'player', 'security', 'country',
            'equipment', 'analysis', 'policy', 'thought', 'strategy', 'direction', 'technology',
            'army', 'fight', 'war', 'freedom', 'failure', 'night',  'day', 'energy', 'nation',
            'moment', 'politics', 'empire', 'president', 'council', 'effort', 'situation',
            'resource', 'influence', 'agreement', 'union', 'religion', 'virus', 'republic',
            'drama', 'tension', 'suspense', 'friendship', 'twilight', 'imperium', 'leadership',
            'operation', 'disaster', 'leader', 'speaker', 'diplomacy', 'politics', 'warfare', 'construction',
            'trade', 'proposal', 'revolution', 'negotiation'
        ];

        return 'Operation ' . ucfirst($adjectives[rand(0, count($adjectives) - 1)]) . ' ' . ucfirst($nouns[rand(0, count($nouns) - 1)]);
    }

    public function toJson(): array
    {
        return [
            'players' => $this->players,
            'include_pok' => $this->include_pok,
            'include_ds_tiles' => $this->include_ds_tiles,
            'include_base_factions' => $this->include_base_factions,
            'include_pok_factions' => $this->include_pok_factions,
            'include_keleres' => $this->include_keleres,
            'include_discordant' => $this->include_discordant,
            'include_discordantexp' => $this->include_discordantexp,
            'num_slices' => $this->num_slices,
            'num_factions' => $this->num_factions,
            'min_wormholes' => $this->min_wormholes,
            'max_1_wormhole' => $this->max_1_wormhole,
            'min_legendaries' => $this->min_legendaries,
            'minimum_optimal_influence' => $this->minimum_optimal_influence,
            'minimum_optimal_resources' => $this->minimum_optimal_resources,
            'minimum_optimal_total' => $this->minimum_optimal_total,
            'maximum_optimal_total' => $this->maximum_optimal_total,
            'custom_factions' => $this->custom_factions,
            'custom_slices' => $this->custom_slices
        ];
    }
}
