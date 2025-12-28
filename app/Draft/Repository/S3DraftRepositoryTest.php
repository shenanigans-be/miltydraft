<?php

declare(strict_types=1);

namespace App\Draft\Repository;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class S3DraftRepositoryTest extends TestCase
{
    // @todo ???
    // Figure out a way to run tests that doesn't require putting secrets in the code

    #[Test]
    public function itDoesntDoTestsForNow_SadFace(): void
    {
        $this->expectNotToPerformAssertions();
    }
}