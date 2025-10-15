<?php

namespace App;

class Station
{
    public $name;
    public $influence;
    public $resources;
    public $optimal_total;
    public $optimal_resources;
    public $optimal_influence;

    function __construct($json_data)
    {
        $this->name = $json_data['name'];
        $this->influence = $json_data['influence'];
        $this->resources = $json_data['resources'];

        // pre-calculate the optimals (same logic as planets)
        if ($this->influence > $this->resources) {
            $this->optimal_influence = $this->influence;
            $this->optimal_resources = 0;
        } elseif ($this->resources > $this->influence) {
            $this->optimal_influence = 0;
            $this->optimal_resources = $this->resources;
        } elseif ($this->resources == $this->influence) {
            $this->optimal_influence = $this->resources / 2;
            $this->optimal_resources = $this->resources / 2;
        }

        $this->optimal_total = $this->optimal_resources + $this->optimal_influence;
    }
}
