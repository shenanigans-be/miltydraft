<?php


namespace App;

class Tile
{
    public $id;
    public $type;
    /**
     * @var Planet[]
     */
    public $planets;
    /**
     * @var Station[]
     */
    public $stations;
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
        $this->hyperlanes = isset($json_data['hyperlanes']) ? $json_data['hyperlanes'] : null;
        $this->anomaly = isset($json_data['anomaly']) ? $json_data['anomaly'] : null;
        $this->planets = [];
        foreach ($json_data['planets'] as $p) {
            $planet = new Planet($p);
            $this->total_influence += $planet->influence;
            $this->total_resources += $planet->resources;
            $this->optimal_influence += $planet->optimal_influence;
            $this->optimal_resources += $planet->optimal_resources;
            $this->planets[] = $planet;
        }

        // Process stations if they exist
        $this->stations = [];
        if (isset($json_data['stations'])) {
            foreach ($json_data['stations'] as $s) {
                $station = new Station($s);
                $this->total_influence += $station->influence;
                $this->total_resources += $station->resources;
                $this->optimal_influence += $station->optimal_influence;
                $this->optimal_resources += $station->optimal_resources;
                $this->stations[] = $station;
            }
        }

        $this->optimal_total = $this->optimal_resources + $this->optimal_influence;
    }

    function hasAnomaly()
    {
        return $this->anomaly != null;
    }

    function hasWormhole($wormhole)
    {
        return $wormhole == $this->wormhole;
    }

    function hasLegendary()
    {
        foreach ($this->planets as $p) {
            if ($p->legendary) return true;
        }

        return false;
    }


    /**
     * @param Tile[] $tiles
     * @return int[]
     */
    public static function countSpecials(array $tiles)
    {
        $alpha_count = 0;
        $beta_count = 0;
        $legendary_count = 0;

        foreach ($tiles as $tile) {
            if ($tile->hasWormhole("alpha")) $alpha_count++;
            if ($tile->hasWormhole("beta")) $beta_count++;
            if ($tile->hasWormhole("alpha-beta")) {
                $alpha_count++;
                $beta_count++;
            }
            if ($tile->hasLegendary()) $legendary_count++;
        }

        return [
            'alpha' => $alpha_count,
            'beta' => $beta_count,
            'legendary' => $legendary_count
        ];
    }
}
