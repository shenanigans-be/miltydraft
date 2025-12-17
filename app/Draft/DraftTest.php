<?php

namespace App\Draft;

use App\Testing\DraftSettingsFactory;
use App\Testing\TestCase;
use App\Testing\TestDrafts;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

class DraftTest extends TestCase
{
    #[DataProviderExternal(TestDrafts::class, "provideTestDrafts")]
    #[Test]
    public function ìtCanBeInitialisedFromJson($data)
    {
        $draft = Draft::fromJson($data);

        // we shouldn't worry about players/settings getting initialised
        // that should be tested in their own class. Just checking that these aren't empty
        $this->assertNotEmpty($draft->players);
        $this->assertSame($data['config']['name'], (string) $draft->settings->name);
        $this->assertSame($draft->id, $data['id']);
        $this->assertSame($draft->isDone, $data['done']);
    }

    #[Test]
    public function itCanBeConvertedToArray()
    {
        $draft = new Draft(
            '1243',
            true,
            [],
            DraftSettingsFactory::make()
        );

        $data = $draft->toArray();

        $this->assertSame($draft->settings->toArray(), $data['config']);
        $this->assertSame($draft->id, $data['id']);
        $this->assertSame($draft->isDone, $data['done']);
    }
}