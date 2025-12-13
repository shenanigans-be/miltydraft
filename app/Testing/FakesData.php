<?php

namespace App\Testing;

use Faker\Factory;
use Faker\Generator;

trait  FakesData
{
    private Generator $faker;

    private function bootFaker() {
        $this->faker = Factory::create();
    }

    protected function faker(): Generator {
        if (!isset($this->faker)) {
            $this->bootFaker();
        }

        return $this->faker;
    }
}