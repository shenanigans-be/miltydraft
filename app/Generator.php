<?php

namespace App;

class Generator
{
    public static function slices($config, $previous_tries = 0)
    {
        if ($previous_tries > 100) {
            return_error("Selection contains no valid slices. This happens occasionally to valid configurations but it probably means that the parameters are impossible.");
        }

        // Gather Tiles
        $all_tiles = self::gather_tiles($config);

        if ($config->custom_slices != null) {
            $tile_data = self::import_tile_data();

            $slices = [];

            foreach ($config->custom_slices as $slice_data) {
                $tiles = [];
                foreach ($slice_data as $tile_id) {
                    $tiles[] = $tile_data[$tile_id];
                }
                $slices[] = new Slice($tiles);
            }

            return self::convert_slices_data($slices);
            //            return $slices;

        } else {
            $selected_tiles = self::select_tiles($all_tiles, $config);
            $slices = self::slicesFromTiles($selected_tiles, $config);

            if ($slices == false) {
                // can't make slices with this selection
                return self::slices($config, $previous_tries + 1);
            } else {
                return self::convert_slices_data($slices);
            }
        }
    }

    private static function convert_slices_data($slices)
    {
        $data = [];

        foreach ($slices as $slice) {
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
    private static function slicesFromTiles($tiles, $config, $previous_tries = 0)
    {
        $slices = [];

        if ($previous_tries > 1000) {
            return false;
        }

        // reshuffle
        shuffle($tiles["high"]);
        shuffle($tiles["mid"]);
        shuffle($tiles["low"]);
        shuffle($tiles["red"]);

        for ($i = 0; $i < $config->num_slices; $i++) {
            // grab some tiles

            $slice = new Slice([
                $tiles['high'][$i],
                $tiles['mid'][$i],
                $tiles['low'][$i],
                $tiles['red'][2 * $i],
                $tiles['red'][(2 * $i) + 1],
            ]);

            if (!$slice->validate($config)) {
                return self::slicesFromTiles($tiles, $config, $previous_tries + 1);
            }

            if ($slice->arrange() == false) {
                // impossible slice, retry
                return self::slicesFromTiles($tiles, $config, $previous_tries + 1);
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
    private static function select_tiles($tiles, $config, $previous_tries = 0)
    {
        $selection_valid = false;

        if ($previous_tries > 1000) {
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
        $counts = Tile::countSpecials($all);

        // validate against minimums
        if ($counts["alpha"] < $config->min_wormholes || $counts["beta"] < $config->min_wormholes || $counts["legendary"] < $config->min_legendaries) {
            // try again
            return self::select_tiles($tiles, $config, $previous_tries + 1);
        } else {
            return $selection;
        }
    }

    /**
     * Import tile tier-listings
     *
     * @param GeneratorConfig $config
     */
    private static function gather_tiles($config)
    {
        $tile_tiers = json_decode(file_get_contents('data/tile-selection.json'), true);
        $tile_data = self::import_tile_data();

        $all_tiles = [
            'high' => [],
            'mid' => [],
            'low' => [],
            'red' => [],
        ];

        foreach ($tile_tiers['tiers'] as $tier => $tiles) {
            foreach ($tiles as $tile_id) {
                $all_tiles[$tier][] = $tile_data[$tile_id];
            }
        }

        if ($config->include_pok) {
            foreach ($tile_tiers['pokTiers'] as $tier => $tiles) {
                foreach ($tiles as $tile_id) {
                    $all_tiles[$tier][] = $tile_data[$tile_id];
                }
            }
        }

        if ($config->include_ds_tiles) {
            foreach ($tile_tiers['DSTiers'] as $tier => $tiles) {
                foreach ($tiles as $tile_id) {
                    $all_tiles[$tier][] = $tile_data[$tile_id];
                }
            }
        }

        return $all_tiles;
    }


    private static function import_faction_data()
    {
        return json_decode(file_get_contents('data/factions.json'), true);
    }

    /**
     * @param GeneratorConfig $config
     */
    public static function factions($config)
    {

        $possible_factions = self::filtered_factions($config);


        if ($config->custom_factions != null) {
            $factions = [];

            foreach ($config->custom_factions as $f) {
                $factions[] = $f;
            }


            // add some more boys and girls untill we reach the magic number
            $i = 0;
            while (count($factions) < $config->num_factions) {
                $f = $possible_factions[$i];

                if (!in_array($f, $factions)) {
                    $factions[] = $f;
                }

                $i++;
            }


            shuffle($factions);
        } else {
            $factions = $possible_factions;
        };


        return array_slice($factions, 0, $config->num_factions);
    }

    public static function filtered_factions($config)
    {

        $faction_data = self::import_faction_data();
        $factions = [];
        foreach ($faction_data as $faction => $data) {
            if ($data["set"] == "base" && $config->include_base_factions) {
                $factions[] = $faction;
            }
            if ($data["set"] == "pok" && $config->include_pok_factions) {
                $factions[] = $faction;
            }
            if ($data["set"] == "keleres" && $config->include_keleres) {
                $factions[] = $faction;
            }
            if ($data["set"] == "discordant" && $config->include_discordant) {
                $factions[] = $faction;
            }
            if ($data["set"] == "discordantexp" && $config->include_discordantexp) {
                $factions[] = $faction;
            }
        }
        shuffle($factions);
        return $factions;
    }


    private static function import_tile_data()
    {
        $data = json_decode(file_get_contents('data/tiles.json'), true);
        $tiles = [];


        foreach ($data as $i => $tile_data) {
            $tiles[$i] = new Tile($i, $tile_data);
        }

        return $tiles;
    }

}