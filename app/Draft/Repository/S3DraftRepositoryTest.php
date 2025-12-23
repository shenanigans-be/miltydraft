<?php

namespace App\Draft\Repository;

use App\Draft\Draft;
use App\Testing\Factories\DraftSettingsFactory;
use App\Testing\TestCase;
use App\Testing\TestDrafts;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

class S3DraftRepositoryTest extends TestCase
{
    // @todo ???
    // Figure out a way to run tests that doesn't require putting secrets in the code

    #[Test]
    public function itDoesntDoTestsForNow_SadFace()
    {
        $this->expectNotToPerformAssertions();
    }
}