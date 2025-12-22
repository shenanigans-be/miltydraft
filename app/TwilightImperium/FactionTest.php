<?php

namespace App\TwilightImperium;

use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;
use function Aws\filter;

/**
 * Historical data integrity and such is tested in data/FactionDataTest
 */
class FactionTest extends TestCase
{
    #[Test]
    public function allFactionsCanBeInitialisedFromJson()
    {
        $rawData = json_decode(file_get_contents('data/factions.json'), true);
        $factions = Faction::all();

        foreach($rawData as $key => $data) {
            $faction = $factions[$key];

            $this->assertSame($faction->name, $data['name']);
            $this->assertSame($faction->id, $data['id']);
            $this->assertSame($faction->homeSystemTileNumber, $data['homesystem']);
            $this->assertSame($faction->linkToWiki, $data['wiki']);
        }
    }
}