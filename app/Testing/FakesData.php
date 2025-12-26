<?php

declare(strict_types=1);

namespace App\Testing;

use Faker\Factory;
use Faker\Generator;

trait  FakesData
{
    private Generator $faker;

    private function bootFaker(): void {
        $this->faker = Factory::create();
    }

    protected function faker(): Generator {
        if (! isset($this->faker)) {
            $this->bootFaker();
        }

        return $this->faker;
    }
}