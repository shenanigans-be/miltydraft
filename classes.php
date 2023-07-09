<?php

class Tile
{
    public $id;
    public $type;
    /**
     * @var Planet[]
     */
    public $planets;
    public $wormhole;
    public $anomaly;
    public $hyperlanes;
    public $total_influence = 0;
    public $total_resources = 0;
    public $optimal_influence = 0;
    public $optimal_resources = 0;
    public $optimal_total = 0;
    public $special_count;

    function __construct($id, $json_data)
    {
        $this->id = $id;
        $this->type = $json_data['type'];
        $this->wormhole = $json_data['wormhole'];
        $this->hyperlanes = isset($json_data['hyperlanes'])? $json_data['hyperlanes'] : null;
        $this->anomaly = isset($json_data['anomaly'])? $json_data['anomaly'] : null;
        $this->planets = [];
        foreach($json_data['planets'] as $p) {
            $planet = new Planet($p);
            $this->total_influence += $planet->influence;
            $this->total_resources += $planet->resources;
            $this->optimal_influence += $planet->optimal_influence;
            $this->optimal_resources += $planet->optimal_resources;
            $this->planets[] = $planet;
        }

        $this->optimal_total = $this->optimal_resources + $this->optimal_influence;
    }

    function has_anomaly() {
        return $this->anomaly != null;
    }

    function has_wormhole($wormhole) {
        return $wormhole == $this->wormhole;
    }

    function has_legendary() {
        foreach($this->planets as $p) {
            if($p->legendary) return true;
        }

        return false;
    }
}

class Planet {
    public $name;
    public $influence;
    public $resources;
    public $legendary;
    public $trait;
    public $specialty;
    public $optimal_total;
    public $optimal_resources;
    public $optimal_influence;

    function __construct($json_data)
    {
        $this->name = $json_data['name'];
        $this->influence = $json_data['influence'];
        $this->resources = $json_data['resources'];
        $this->legendary = $json_data['legendary'];
        $this->trait = isset($json_data["trait"])? $json_data['trait'] : null;
        $this->specialty = $json_data["specialty"];

        // pre-calculate the optimals
        if($this->influence > $this->resources) {
            $this->optimal_influence = $this->influence;
            $this->optimal_resources = 0;
        } elseif($this->resources > $this->influence) {
            $this->optimal_influence = 0;
            $this->optimal_resources = $this->resources;
        } elseif($this->resources == $this->influence) {
            $this->optimal_influence = $this->resources / 2;
            $this->optimal_resources = $this->resources / 2;
        }

        $this->optimal_total = $this->optimal_resources + $this->optimal_influence;
    }
}

class Slice {
    public $tiles;
    public $tile_ids = [];
    public $total_influcence = 0;
    public $total_resources = 0;
    public $optimal_influence = 0;
    public $optimal_resources = 0;
    public $total_optimal = 0;
    public $wormholes = [];
    public $specialties = [];

    /**
     * Slice constructor.
     * @param Tile[] $tiles
     */
    function __construct($tiles)
    {
        $this->tiles = $tiles;
        foreach($tiles as $tile) {

            $this->tile_ids[] = $tile->id;
            $this->total_influcence += $tile->total_influence;
            $this->total_resources += $tile->total_resources;
            $this->optimal_influence += $tile->optimal_influence;
            $this->optimal_resources += $tile->optimal_resources;

            if($tile->wormhole != null) $this->wormholes[] = $tile->wormhole;

            foreach($tile->planets as $planet) {
                if($planet->specialty != null) {
                    $this->specialties[] = $planet->specialty;
                }
            }
        }

        $this->total_optimal = $this->optimal_resources + $this->optimal_influence;
    }

    function toJson() {
        return [
            'tiles' => $this->tile_ids,
            'specialties' => $this->specialties,
            'wormholes' => $this->wormholes,
            'has_legendaries' => count_specials($this->tiles)['legendary'] > 0,
            'total_influence' => $this->total_influcence,
            'total_resources' => $this->total_resources,
            'optimal_influence' => $this->optimal_influence,
            'optimal_resources' => $this->optimal_resources
        ];
    }

    /**
     * @param GeneratorConfig $config
     * @return bool
     */
    function validate($config) {
        $special_count = count_specials($this->tiles);

        // can't have 2 alpha, beta or legendaries
        if($special_count['alpha'] > 1 || $special_count['beta'] > 1 || $special_count['legendary'] > 1) {
            return false;
        }

        // has the right minimum optimal values?
        if($this->optimal_influence < $config->minimum_optimal_influence || $this->optimal_resources < $config->minimum_optimal_resources) {
            //echo "not enough minimum<br />";
            return false;
        }

        if($special_count['alpha'] > 0 && $special_count['beta'] > 0 && $config->max_1_wormhole) {
            return false;
        }

        // has the right total optimal value? (not too much, not too little)
        if($this->total_optimal < $config->minimum_optimal_total || $this->total_optimal > $config->maximum_optimal_total) {
            return false;
        }

        return true;
    }

    function arrange($previous_tries = 0)
    {
        // miltydraft.com only shuffles it 12 times max, no idea why but we're gonna assume they have a reason
        if ($previous_tries > 12) {
            return false;
        }

        shuffle($this->tiles);
        // tiles are laid out like this:
        //      4
        //  3
        //      1
        //  0       2
        //      H
        // so for example, tile #1 neighbours #0, #3 and #4. #2 only neighbours #1

        $neighbours = [[0, 1], [0, 3], [1, 2], [1, 3], [1, 4], [3, 4]];

        foreach ($neighbours as $edge) {
            // can't have two neighbouring anomalies
            if ($this->tiles[$edge[0]]->has_anomaly() && $this->tiles[$edge[1]]->has_anomaly()) {
                return $this->arrange($previous_tries + 1);
            }
        }

        // if we're all good at this point then we should fix the order of the tile ids
        $this->tile_ids = [];
        foreach($this->tiles as $t) {
            $this->tile_ids[] = $t->id;
        }

        return true;
    }
}



class GeneratorConfig {
    public $players = [];
    public $num_players;
    public $num_slices;
    public $num_factions;
    public $include_pok;
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
        if($get_values_from_request) {
            $names = get('player', []);
            shuffle($names);
            foreach($names as $name) {
                if($name != '')  $this->players[] = htmlentities($name);
            }

            $this->name = get('game_name', '');
            if(trim($this->name) == '') $this->name = $this->generate_name();
            else $this->name = htmlentities($this->name);
            $this->num_players = (int) get('num_players');
            $this->num_slices = (int) get('num_slices');
            $this->num_factions = (int) get('num_factions');
            $this->include_pok = get('include_pok') == true;
            $this->include_base_factions = get('include_base_factions') == true;
            $this->include_pok_factions = get('include_pok_factions') == true;
            $this->include_keleres = get('include_keleres') == true;
            $this->include_discordant = get('include_discordant') == true;
            $this->include_discordantexp = get('include_discordantexp') == true;

            $this->max_1_wormhole = get('max_wormhole') == true;
            $this->min_wormholes = (get('wormholes') == true)? 2 : 0;
            $this->min_legendaries = (int) get('legendary');

            $this->minimum_optimal_influence = (float) get('min_inf');
            $this->minimum_optimal_resources = (float) get('min_res');
            $this->minimum_optimal_total = (float) get('min_total');
            $this->maximum_optimal_total = (float) get('max_total');

            if(!empty(get('custom_factions', []))) {
                $this->custom_factions = get('custom_factions');
            }

            if(get('custom_slices') != '') {
                $slice_data = explode("\n", get('custom_slices'));
                $this->custom_slices = [];
                foreach($slice_data as $s) {
                    $slice = [];
                    $t = explode(',', $s);
                    foreach($t as $tile) {
                        $tile = trim($tile);
                        $slice[] = $tile;
                    }
                    $this->custom_slices[] = $slice;
                }
            }

            $this->validate();
        }

    }


    static function fromDraft($draft)
    {
        $config = new GeneratorConfig(false);
        $config->players = $draft['config']['players'];

        $config->name = $draft['name'];
        $config->num_players = count($config->players);
        $config->num_slices = $draft['config']['num_slices'];
        $config->num_factions = $draft['config']['num_factions'];
        $config->include_pok = $draft['config']['include_pok'];
        $config->include_base_factions = $draft['config']['include_base_factions'];
        $config->include_pok_factions = $draft['config']['include_pok_factions'];
        $config->include_keleres = $draft['config']['include_keleres'];
        $config->include_discordant = $draft['config']['include_discordant'];
        $config->include_discordantexp = $draft['config']['include_discordantexp'];
//        dd($config);

        $config->max_1_wormhole = $draft['config']['max_1_wormhole'];
        $config->min_wormholes = $draft['config']['min_wormholes'];
        $config->min_legendaries = $draft['config']['min_legendaries'];

        $config->minimum_optimal_influence = $draft['config']['minimum_optimal_influence'];
        $config->minimum_optimal_resources = $draft['config']['minimum_optimal_resources'];
        $config->minimum_optimal_total = $draft['config']['minimum_optimal_total'];
        $config->maximum_optimal_total = $draft['config']['maximum_optimal_total'];

        $config->custom_factions = $draft['config']['custom_factions'];
        $config->custom_slices = $draft['config']['custom_slices'];

        $config->validate();

        return $config;
    }

    function validate() {

        if(count($this->players) < $this->num_players) return_error('Some players names are not filled out');
        if(!$this->include_pok && $this->num_slices > 5) return_error('Can only draft up to 5 slices without PoK. (And by extension you can only do drafts up to 5 players)');
        if($this->num_players < 3) return_error('Please enter more than 3 players');
        if($this->num_factions < $this->num_players) return_error("Can't have less factions than players");
        if($this->num_slices < $this->num_players) return_error("Can't have less slices than players");
        if($this->maximum_optimal_total < $this->minimum_optimal_total) return_error("Maximum optimal can't be less than minimum");
        // Must include at least 1 of base, pok, discordant, or discordant expansion to have enough factions to use
        if(!($this->include_base_factions || $this->include_pok_factions || $this->include_discordant || $this->include_discordantexp)) return_error("Not enough factions selected.");
        // if($this->custom_factions != null && count($this->custom_factions) < $this->num_players) return_error("Not enough custom factions for number of players");
        if($this->custom_slices != null) {
            if(count($this->custom_slices) < $this->num_players) return_error("Not enough custom slices for number of players");
            foreach($this->custom_slices as $s) {
                if(count($s) != 5) return_error('Some of the custom slices have the wrong number of tiles. (each should have five)');
            }

        }
    }

    function generate_name() {
        $adjectives = ['adventurous', 'aggressive', 'angry', 'arrogant', 'beautiful', 'bloody', 'blushing', 'brave',
            'clever', 'clumsy', 'combative', 'confused', 'crazy', 'curious', 'defiant', 'difficult', 'disgusted', 'doubtful', 'easy',
            'famous',  'fantastic', 'filthy', 'frightened', 'funny', 'glamorous', 'gleaming', 'glorious',
            'grumpy', 'homeless', 'hilarious', 'impossible', 'itchy', 'imperial', 'jealous', 'long', 'magnificent', 'lucky',
            'modern', 'mysterious', 'naughty', 'old-fashioned', 'outstanding', 'outrageous', 'perfect',
            'poisoned', 'puzzled', 'rich', 'smiling', 'super', 'tasty', 'terrible', 'wandering', 'zealous'];
        $nouns = ['people', 'history', 'art', 'world', 'space', 'universe', 'galaxy', 'story',
                'map', 'game', 'family', 'government', 'system', 'method', 'computer', 'problem',
            'theory', 'law', 'power', 'knowledge', 'control', 'ability', 'love', 'science',
            'fact', 'idea', 'area', 'society', 'industry', 'player', 'security', 'country',
            'equipment', 'analysis', 'policy', 'thought', 'strategy', 'direction', 'technology',
            'army', 'fight', 'war', 'freedom', 'failure', 'night',  'day', 'energy', 'nation',
            'moment', 'politics', 'empire', 'president', 'council', 'effort', 'situation',
            'resource', 'influence', 'agreement', 'union', 'religion', 'virus', 'republic',
            'drama', 'tension', 'suspense', 'friendship', 'twilight', 'imperium', 'leadership',
            'operation', 'disaster', 'leader', 'speaker', 'diplomacy', 'politics', 'warfare', 'construction',
            'trade', 'proposal', 'revolution', 'negotiation'];

        return 'Operation ' . ucfirst( $adjectives[rand(0, count($adjectives) - 1)]) . ' ' . ucfirst($nouns[rand(0, count($nouns) - 1)]);
    }

    function toJson() {
        return [
            'players' => $this->players,
            'include_pok' => $this->include_pok,
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


?>
