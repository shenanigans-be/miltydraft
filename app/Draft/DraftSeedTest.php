<?php

namespace App\Draft;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DraftSeedTest extends TestCase
{
    private const TEST_SEED = 123;
    private const TEST_SLICE_TRIES = 3;

    #[Test]
    public function itCanGenerateASeed()
    {
        $seed = new DraftSeed();
        $this->assertIsInt($seed->getValue());
    }

    #[Test]
    public function itCanUseAUserSeed()
    {
        $seed = new DraftSeed(self::TEST_SEED);
        $this->assertSame(self::TEST_SEED, $seed->getValue());
    }

    #[Test]
    public function itCanSetTheFactionSeed()
    {
        $seed = new DraftSeed(self::TEST_SEED);
        $seed->setForFactions();
        $n = mt_rand(1, 10000);
        // pre-calculated using TEST_SEED
        $this->assertSame(5295, $n);
    }

    #[Test]
    public function itCanSetTheSliceSeed()
    {
        $seed = new DraftSeed(self::TEST_SEED);
        $seed->setForSlices(self::TEST_SLICE_TRIES);
        $n = mt_rand(1, 10000);
        // pre-calculated using TEST_SEED
        $this->assertSame(823, $n);
    }

    #[Test]
    public function itCanSetThePlayerOrderSeed()
    {
        $seed = new DraftSeed(self::TEST_SEED);
        $seed->setForPlayerOrder();
        $n = mt_rand(1, 10000);
        // pre-calculated using TEST_SEED
        $this->assertSame(1646, $n);
    }

    #[Test]
    public function arraysAreShuffledPredictablyWhenSeedIsSet()
    {
        $seed = new DraftSeed(self::TEST_SEED);
        $seed->setForFactions();

        $a = [
            "a", "b", "c", "d", "f", "e", "g"
        ];

        shuffle($a);

        // pre-calculated using TEST_SEED + factions
        $newOrder = [
            "a", "g", "e", "f", "d", "c", "b"
        ];

        foreach ($a as $idx => $value) {
            $this->assertSame($newOrder[$idx], $a[$idx]);
        }
    }
}