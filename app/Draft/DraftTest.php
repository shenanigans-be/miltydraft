<?php

namespace App\Draft;

use App\Testing\Factories\DraftSettingsFactory;
use App\Testing\TestCase;
use App\Testing\TestDrafts;
use App\TwilightImperium\Faction;
use App\TwilightImperium\Tile;
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

        $factionPoolNames = array_map(fn (Faction $f) => $f->name, $draft->factionPool);

        foreach($data['factions'] as $faction) {
            $this->assertContains($faction, $factionPoolNames);
        }
        $this->assertSame($draft->currentPlayerId->value, $data['draft']['current']);
    }

    #[Test]
    public function itCanBeConvertedToArray()
    {
        $factions = Faction::all();
        $tiles = Tile::all();
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
            [
               new Slice([
                   $tiles["64"],
                   $tiles["33"],
                   $tiles["42"],
                   $tiles["67"],
                   $tiles["59"],
               ]),
            ],
            [
                $factions['The Barony of Letnev'],
                $factions['The Embers of Muaat'],
                $factions['The Clan of Saar']
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
            $this->assertContains($faction->name, $data['factions']);
        }
        foreach($draft->slicePool as $slice) {
            $this->assertContains($slice->tileIds(), $data['slices']);
        }
    }
}