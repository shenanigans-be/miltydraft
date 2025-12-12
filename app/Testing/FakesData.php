<?php

namespace App\Testing;

use Faker\Factory;
use Faker\Generator;

trait  FakesData
{
    protected Generator $faker;

    protected function bootFaker() {
        $this->faker = Factory::create();
    }
}