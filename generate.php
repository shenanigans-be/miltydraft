<?php
    require_once 'boot.php';



    $config = new GeneratorConfig();

    generate($config);

    /**
     * @param GeneratorConfig $config
     * @param int $previous_tries
     * @return mixed
     */
    function generate($config, $previous_tries = 0) {
        if($previous_tries > 100) {
            return_error("Selection contains no valid slices. This happens occasionally to valid configurations but it probably means that the parameters are impossible.");
        }

        // Gather Tiles
        $all_tiles = gather_tiles($config);

        if($config->custom_slices != null) {
            $tile_data = import_tile_data();

            $slices = [];

            foreach($config->custom_slices as $slice_data) {
                $tiles = [];
                foreach($slice_data as $tile_id) {
                    $tiles[] = $tile_data[$tile_id];
                }
                $slices[] = new Slice($tiles);
            }
        } else {
            $selected_tiles = select_tiles($all_tiles, $config);
            $slices = generate_slices($selected_tiles, $config);

            if($slices == false) {
                // can't make slices with this selection
                return generate($config, $previous_tries + 1);
            }
        }

        $factions = select_factions($config);

        $player_data = [];
        $first_player = null;
        foreach($config->players as $i => $p) {
            $id = 'p_' . uniqid();

            if($i == 0) {
                $first_player = $id;
            }

            $player_data[$id] = [
                'id' => $id,
                'name' => $p,
                'claimed' => false,
                'position' => null,
                'slice' => null,
                'faction' => null
            ];
        }


        $id = uniqid();
        $admin_password = uniqid();

        $result = [
            'id' => $id,
            'factions' => $factions,
            'admin_pass' => $admin_password,
            'config' => $config->toJson(),
            'slices' => convert_slices_data($slices),
            'url' => 'https://' . $_ENV['BUCKET'] . '.' . $_ENV['REGION'] . '.digitaloceanspaces.com/draft_' . $id . '.json',
            'draft' => [
                'players' => $player_data,
                'current' => $first_player,
                'index' => 0,
                'log' => [],
                'order_reversed' => false
            ]
        ];

        save_draft($result);

        return_data([
            'id' => $id,
            'admin' => $admin_password
        ]);
    }

    function convert_slices_data($slices) {
        $data = [];

        foreach($slices as $slice) {
            $data[] = $slice->toJson();
        }

        return $data;
    }

    /**
     * @param $tiles
     * @param GeneratorConfig $config
     * @param int $previous_tries
     * @return mixed
     */
    function generate_slices($tiles, $config, $previous_tries = 0) {
        $slices = [];

        if($previous_tries > 1000) {
            return false;
        }

        // reshuffle
        shuffle($tiles["high"]);
        shuffle($tiles["mid"]);
        shuffle($tiles["low"]);
        shuffle($tiles["red"]);

        for($i = 0; $i < $config->num_slices; $i++) {
            // grab some tiles

            $slice = new Slice([
                $tiles['high'][$i],
                $tiles['mid'][$i],
                $tiles['low'][$i],
                $tiles['red'][2 * $i],
                $tiles['red'][(2 * $i) + 1],
            ]);

            if(!$slice->validate($config)) {
                return generate_slices($tiles, $config, $previous_tries + 1);
            }

            if($slice->arrange() == false) {
                // impossible slice, retry
                return generate_slices($tiles, $config,$previous_tries + 1);
            }

            // all good!
            $slices[] = $slice;
        }

        return $slices;
    }


    /**
     * Make a tile selection based on tier-listing
     *
     * @param array $tiles
     * @param GeneratorConfig $config
     * @param int $previous_tries
     * @return array
     */
    function select_tiles($tiles, $config, $previous_tries = 0) {
        $selection_valid = false;

        if($previous_tries > 1000) {
            return_error("No valid selection");
        }

        $min_alpha = 0;
        $min_beta = 0;
        $min_legend = 0;

        // randomly determine a minimum amount of wormholes and legendaries
        if($config->must_include_wormholes_and_legendaries) {
            $min_alpha = round(rand(2, 3));
            $min_beta = round(rand(2, 3));
            $min_legend = round(rand(1, 2));
        }

        shuffle($tiles['high']);
        shuffle($tiles['mid']);
        shuffle($tiles['low']);
        shuffle($tiles['red']);

        $selection = [
            'high' => array_slice($tiles["high"], 0, $config->num_slices),
            'mid' => array_slice($tiles["mid"], 0, $config->num_slices),
            'low' => array_slice($tiles["low"], 0, $config->num_slices),
            'red' => array_slice($tiles["red"], 0, $config->num_slices * 2),
        ];


        $all = array_merge($selection["high"], $selection["mid"], $selection["low"], $selection["red"]);

        // check if the wormhole/legendary count is high enough
        if($config->must_include_wormholes_and_legendaries) {
            // count stuff
            $counts = count_specials($all);

            // validate against minimums
            if($counts["alpha"] < $min_alpha || $counts["alpha"] < $min_beta || $counts["legendary"] < $min_legend) {
                // try again
                return select_tiles($tiles, $config,$previous_tries + 1);
            } else {
                return $selection;
            }
        } else {
            return $selection;
        }
    }

    /**
     * Import tile tier-listings
     *
     * @param GeneratorConfig $config
     */
    function gather_tiles($config) {
        $tile_tiers = json_decode(file_get_contents('data/tile-selection.json'), true);
        $tile_data = import_tile_data();

        $all_tiles = [
            'high' => [],
            'mid' => [],
            'low' => [],
            'red' => [],
        ];

        foreach($tile_tiers['tiers'] as $tier => $tiles) {
            foreach($tiles as $tile_id) {
                $all_tiles[$tier][] = $tile_data[$tile_id];
            }
        }

        if($config->include_pok) {
            foreach($tile_tiers['pokTiers'] as $tier => $tiles) {
                foreach($tiles as $tile_id) {
                    $all_tiles[$tier][] = $tile_data[$tile_id];
                }
            }
        }

        return $all_tiles;
    }

    function count_specials($tiles) {
        $alpha_count = 0;
        $beta_count = 0;
        $legendary_count = 0;

        foreach($tiles as $tile) {

            if($tile->has_wormhole("alpha")) $alpha_count++;
            if($tile->has_wormhole("beta")) $beta_count++;
            if($tile->has_legendary()) $legendary_count++;
        }

        return [
            'alpha' => $alpha_count,
            'beta' => $beta_count,
            'legendary' => $legendary_count
        ];
    }

    function import_faction_data() {
        return json_decode(file_get_contents('data/factions.json'), true);
    }

    /**
     * @param GeneratorConfig $config
     */
    function select_factions($config) {

       $faction_data = import_faction_data();

       $factions = [];

       if($config->custom_factions != null) {
           foreach($config->custom_factions as $f) {
               $factions[] = $f;
           }

           shuffle($faction_data);

           // add some more boys and girls untill we reach the magic number
           $i = 0;
           while(count($factions) < $config->num_factions) {
               $faction = $faction_data[$i]['name'];

               if(!in_array($faction, $factions)) {
                   $factions[] = $faction;
               }

               $i++;
           }
       } else {
           foreach($faction_data as $faction => $data) {
               if($data["set"] == "base") {
                   $factions[] = $faction;
               }
               if($data["set"] == "pok" && $config->include_pok) {
                   $factions[] = $faction;
               }
               if($data["set"] == "keleres" && $config->include_keleres) {
                   $factions[] = $faction;
               }
           }

       }

       shuffle($factions);

       return array_slice($factions, 0, $config->num_factions);
    }

    function import_tile_data() {
        $data = json_decode(file_get_contents('data/tiles.json'), true);
        $tiles = [];


        foreach($data as $i => $tile_data) {
            $tiles[$i] = new Tile($i, $tile_data);
        }

        return $tiles;
    }


?>
