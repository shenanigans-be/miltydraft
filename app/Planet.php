<?php

namespace App;

class Planet
{
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
        $this->trait = isset($json_data["trait"]) ? $json_data['trait'] : null;
        $this->specialty = $json_data["specialty"];

        // pre-calculate the optimals
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
