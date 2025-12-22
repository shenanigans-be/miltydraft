<?php

namespace App\Draft;

use App\Testing\Factories\DraftSettingsFactory;
use App\Testing\TestCase;
use App\Testing\TestDrafts;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

/**
 * We shouldn't worry about players/settings getting initialised or serialised again
 * That should be tested in their own class. Just checking that they're in the right place.
 */
class DraftTest extends TestCase
{
    #[DataProviderExternal(TestDrafts::class, "provideTestDrafts")]
    #[Test]
    public function ìtCanBeInitialisedFromJson($data)
    {
        $draft = Draft::fromJson($data);

        $this->assertNotEmpty($draft->players);
        $this->assertSame($data['config']['name'], (string) $draft->settings->name);
        $this->assertSame($draft->id, $data['id']);
        $this->assertSame($draft->isDone, $data['done']);
        foreach($data['factions'] as $faction) {
            $this->assertContains($faction, $draft->factionPool);
        }
        $this->assertSame($draft->currentPlayerId->value, $data['draft']['current']);
    }

    #[Test]
    public function itCanBeConvertedToArray()
    {
        $player =  new Player(
            PlayerId::fromString("player_123"),
            "Alice"
        );
        $draft = new Draft(
            '1243',
            true,
            [$player->id->value => $player],
            DraftSettingsFactory::make(),
            new Secrets(
                'secret123',
            ),
            [],
            [
                "Mahact",
                "Vulraith",
                "Xxcha"
            ],
            [new Pick($player->id, PickCategory::FACTION, "Vulraith")],
            $player->id
        );

        $data = $draft->toArray();

        $this->assertSame($draft->settings->toArray(), $data['config']);
        $this->assertSame($draft->id, $data['id']);
        $this->assertSame($draft->isDone, $data['done']);
        $this->assertSame($player->name, $data['draft']['players'][$player->id->value]['name']);
        $this->assertSame($player->id->value, $data['draft']['current']);
        $this->assertSame("Vulraith", $data['draft']['log'][0]['value']);
        foreach($draft->factionPool as $faction) {
            $this->assertContains($faction, $data['factions']);
        }
    }
}