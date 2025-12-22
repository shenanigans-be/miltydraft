<?php

namespace App;

use App\Draft\Name;

/**
 * @deprecated
 */
class GeneratorConfig
{
    // Maximum value for random seed generation (2^50)
    // Limited by JavaScript's Number.MAX_SAFE_INTEGER (2^53 - 1) for JSON compatibility
    public const MAX_SEED_VALUE = 1125899906842624;

    const NUM_BASE_BLUE=20;
    const NUM_BASE_RED=12;
    const NUM_POK_BLUE=16;
    const NUM_POK_RED=6;
    const NUM_POK_LEGENDARIES=2;
    const NUM_DS_BLUE=16;
    const NUM_DS_RED=8;
    const NUM_DS_LEGENDARIES=5;
    const NUM_TE_BLUE=15;
    const NUM_TE_RED=5;
    const NUM_TE_LEGENDARIES=5;

    public $players = [];
    public $name;
    public $num_slices;
    public $num_factions;
    public $include_pok;
    public $include_ds_tiles;
    public $include_te_tiles;
    public $include_base_factions;
    public $include_pok_factions;
    public $include_keleres;
    public $include_discordant;
    public $include_discordantexp;
    public $include_te_factions;
    public $preset_draft_order;

    public $min_wormholes;
    public $min_legendaries;
    public $max_1_wormhole;

    public $minimum_optimal_influence;
    public $minimum_optimal_resources;
    public $minimum_optimal_total;
    public $maximum_optimal_total;

    public $custom_factions = null;
    public $custom_slices = null;

    public ?array $alliance = null;
    public ?int $seed = null;

    function __construct($get_values_from_request)
    {
        if ($get_values_from_request) {

            $this->players = array_filter(array_map('htmlentities', get('player', [])));
            if ((int) get('num_players') != count($this->players)) {
                return_error('Number of players does not match number of names');
            }

            $this->name = new Name(get('game_name', ''));
            $this->num_slices = (int) get('num_slices');
            $this->num_factions = (int) get('num_factions');
            $this->include_pok = get('include_pok') == true;
            $this->include_ds_tiles = get('include_ds_tiles') == true;
            $this->include_te_tiles = get('include_te_tiles') == true;
            $this->include_base_factions = get('include_base_factions') == true;
            $this->include_pok_factions = get('include_pok_factions') == true;
            $this->include_keleres = get('include_keleres') == true;
            $this->include_discordant = get('include_discordant') == true;
            $this->include_discordantexp = get('include_discordantexp') == true;
            $this->include_te_factions = get('include_te_factions') == true;
            $this->preset_draft_order = get('preset_draft_order', false) == true;

            $this->max_1_wormhole = get('max_wormhole') == true;
            $this->min_wormholes = (get('wormholes') == true) ? 2 : 0;
            $this->min_legendaries = (int) get('min_legendaries');

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

            if ((bool) get('alliance_on', false)) {
                $this->alliance = [];
                $this->alliance["alliance_teams"] = get('alliance_teams');
                $this->alliance["alliance_teams_position"] = get('alliance_teams_position');
                $this->alliance["force_double_picks"] = get('force_double_picks') == 'true';
            }

            $seed_input = get('seed', '');
            if ($seed_input !== '' && $seed_input !== null) {
                $this->seed = (int) $seed_input;
            } else {
                $this->seed = mt_rand(1, self::MAX_SEED_VALUE);
            }

            $this->validate();
        }
    }

    public static function fromArray(array $array): GeneratorConfig
    {
        $config = new GeneratorConfig(false);

        foreach ($array as $key => $value) {
            $config->$key = $value;
        }

        $config->validate();

        return $config;
    }

    private function validate(): void
    {

        if (count($this->players) > count(array_filter($this->players))) return_error('Some player names are not filled out');
        if (count(array_unique($this->players)) != count($this->players)) return_error('Players should all have unique names');
        $num_tiles = self::NUM_BASE_BLUE + $this->include_pok*self::NUM_POK_BLUE + $this->include_ds_tiles*self::NUM_DS_BLUE + $this->include_te_tiles*self::NUM_TE_BLUE;
        $num_red = self::NUM_BASE_RED + $this->include_pok*self::NUM_POK_RED + $this->include_ds_tiles*self::NUM_DS_RED + $this->include_te_tiles*self::NUM_TE_RED;
        // maximum number of possible slices, 3 blue and 2 red tiles per slice
        $max_slices = min(floor($num_tiles/3), floor($num_red/2));
        if ($max_slices < $this->num_slices) return_error('Can only draft up to ' . $max_slices . ' slices with the selected tiles. (And by extension you can only do drafts up to ' . $max_slices . ' players)');
        if (count($this->players) < 3) return_error('Please enter at least 3 players');
        if ($this->num_factions < count($this->players)) return_error("Can't have less factions than players");
        if ($this->num_slices < count($this->players)) return_error("Can't have less slices than players");
        if ($this->maximum_optimal_total < $this->minimum_optimal_total) return_error("Maximum optimal can't be less than minimum");
        $max_legendaries = $this->include_pok*self::NUM_POK_LEGENDARIES + $this->include_ds_tiles*self::NUM_DS_LEGENDARIES + $this->include_te_tiles*self::NUM_TE_LEGENDARIES;
        if ($max_legendaries < $this->min_legendaries) return_error('Cannot include ' . $this->min_legendaries . ' legendaries, maximum number available is ' . $max_legendaries);
        if ($this->min_legendaries > $this->num_slices) return_error('Cannot include more legendaries than slices');
        if ($this->seed !== null && ($this->seed < 1 || $this->seed > self::MAX_SEED_VALUE)) return_error('Seed must be between 1 and ' . self::MAX_SEED_VALUE);
        // Must include at least 1 of base, pok, discordant, or discordant expansion to have enough factions to use
        if (!($this->include_base_factions || $this->include_pok_factions || $this->include_discordant || $this->include_discordantexp || $this->include_te_factions)) return_error("Not enough factions selected.");
        // if($this->custom_factions != null && count($this->custom_factions) < count($this->players)) return_error("Not enough custom factions for number of players");
        if ($this->custom_slices != null) {
            if (count($this->custom_slices) < count($this->players)) return_error("Not enough custom slices for number of players");
            foreach ($this->custom_slices as $s) {
                if (count($s) != 5) return_error('Some of the custom slices have the wrong number of tiles. (each should have five)');
            }
        }
    }

    public function toJson(): array
    {
        return get_object_vars($this);
    }
}
