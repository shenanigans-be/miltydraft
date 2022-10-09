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
    public $include_keleres;
    public $must_include_wormholes_and_legendaries;
    public $minimum_optimal_influence;
    public $minimum_optimal_resources;
    public $minimum_optimal_total;
    public $maximum_optimal_total;

    function __construct()
    {
        $names = get('player', []);
        shuffle($names);
        foreach($names as $name) {
            if($name != '')  $this->players[] = $name;
        }

        $this->num_players = (int) get('num_players');
        $this->num_slices = (int) get('num_slices');
        $this->num_factions = (int) get('num_factions');
        $this->include_pok = get('include_pok') == true;
        $this->include_keleres = get('include_keleres') == true;
        $this->must_include_wormholes_and_legendaries = get('specials') == true;
        $this->minimum_optimal_influence = (float) get('min_inf');
        $this->minimum_optimal_resources = (float) get('min_res');
        $this->minimum_optimal_total = (float) get('min_total');
        $this->maximum_optimal_total = (float) get('max_total');

        $this->validate();
    }

    function validate() {

        if(count($this->players) != $this->num_players) return_error('Some players names are not filled out');
        if($this->num_players < 3) return_error('Please enter more than 3 players');
        if($this->num_factions < $this->num_players) return_error("Can't have less factions than players");
        if($this->num_slices < $this->num_players) return_error("Can't have less slices than players");
        if($this->maximum_optimal_total < $this->minimum_optimal_total) return_error("Maximum optimal can't be less than minimum");
    }

    function toJson() {
        return [
            'players' => $this->players,
            'include_pok' => $this->include_pok,
            'include_keleres' => $this->include_keleres,
            'num_slices' => $this->num_slices,
            'num_factions' => $this->num_factions,
            'must_include_wormholes_and_legendaries' => $this->must_include_wormholes_and_legendaries,
            'minimum_optimal_influence' => $this->minimum_optimal_influence,
            'minimum_optimal_resources' => $this->minimum_optimal_resources,
            'minimum_optimal_total' => $this->minimum_optimal_total,
            'maximum_optimal_total' => $this->maximum_optimal_total
        ];
    }
}


?>
