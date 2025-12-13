<?php

namespace App\Testing;

use PHPUnit\Framework\Attributes\Before;
use \PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    // unused right now, but we can do stuff like initialize traits and whatnot here

    /*
    #[Before]
    protected function setUpTraits(): void
    {
        require_once 'bootstrap/helpers.php';

        // $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[FakesData::class])) {
            $this->bootFaker();
        }
    }
    */
}