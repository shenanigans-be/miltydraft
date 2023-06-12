<?php
    require_once 'boot.php';

//    dd($_POST);
    if(get('regen') != null) {
        $draft = get_draft(get('regen'));

//        dd($draft['draft']['log']);

        if($draft['admin_pass'] != get('admin')) return_error('You are not allowed to do this');
        if(!empty($draft['draft']['log'])) return_error('Draft already in progress');

        // claimed players?

        $config = GeneratorConfig::fromDraft($draft);
    } else {
        $config = new GeneratorConfig(true);
    }

    if(get('regen') != null) {
        $regen_slices = (bool) get('shuffle_slices', false);
        $regen_factions = (bool) get('shuffle_factions', false);
        $regen_players = (bool) get('shuffle_players', false);
        regenerate($draft, $config, $regen_slices, $regen_factions, $regen_players);
    } else {
        generate($config);
    }

    function regenerate($draft, $config, $regen_slices, $regen_factions, $regen_players) {
        $slices = select_slices($config);
        $factions = select_factions($config);

//        $draft
        if($regen_factions) {
            $draft['factions'] = select_factions($config);
        }
        if($regen_slices) {
            $draft['slices'] = select_slices($config);
        }



        save_draft($draft);

        return_data([
            'ok' => true
        ]);
    }


    /**
     * @param GeneratorConfig $config
     * @param int $previous_tries
     * @return mixed
     */
    function generate($config, $previous_tries = 0) {
        $slices = select_slices($config);
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
            'name' => $config->name,
            'factions' => $factions,
            'admin_pass' => $admin_password,
            'config' => $config->toJson(),
            'slices' => $slices,
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

    function select_slices($config, $previous_tries = 0) {
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

            return convert_slices_data($slices);
//            return $slices;

        } else {
            $selected_tiles = select_tiles($all_tiles, $config);
            $slices = generate_slices($selected_tiles, $config);

            if($slices == false) {
                // can't make slices with this selection
                return select_slices($config,$previous_tries + 1);
            } else {
                return convert_slices_data($slices);
            }
        }
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
        $counts = count_specials($all);

        // validate against minimums
        if($counts["alpha"] < $config->min_wormholes || $counts["beta"] < $config->min_wormholes || $counts["legendary"] < $config->min_legendaries) {
            // try again
            return select_tiles($tiles, $config,$previous_tries + 1);
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

       $possible_factions = filtered_factions($config);


       if($config->custom_factions != null) {
           $factions = [];

           foreach($config->custom_factions as $f) {
               $factions[] = $f;
           }


           // add some more boys and girls untill we reach the magic number
           $i = 0;
           while(count($factions) < $config->num_factions) {
               $f = $possible_factions[$i];

               if(!in_array($f, $factions)) {
                   $factions[] = $f;
               }

               $i++;
           }
       } else {
           $factions = $possible_factions;
       }


       return array_slice($factions, 0, $config->num_factions);
    }

    function filtered_factions($config) {

        $faction_data = import_faction_data();
        $factions = [];
        foreach($faction_data as $faction => $data) {
            if($data["set"] == "base" && $config->include_base_factions) {
                $factions[] = $faction;
            }
            if($data["set"] == "pok" && $config->include_pok_factions) {
                $factions[] = $faction;
            }
            if($data["set"] == "keleres" && $config->include_keleres) {
                $factions[] = $faction;
            }
            if($data["set"] == "discordant" && $config->include_discordant) {
                $factions[] = $faction;
            }
            if($data["set"] == "discordantexp" && $config->include_discordantexp) {
                $factions[] = $faction;
            }
        }
        shuffle($factions);
        return $factions;


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
