<?php

namespace data;

use App\TwilightImperium\Planet;
use App\TwilightImperium\SpaceStation;
use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\Test;

class FactionDataTest extends TestCase
{
    protected function getJsonData(): array
    {
        return json_decode(file_get_contents('data/factions.json'), true);
    }

    #[Test]
    public function eachFactionHasData() {
        $factions = $this->getJsonData();

        foreach($factions as $faction) {
            $this->assertNotEmpty($faction['set']);
            // fix data, then enable this
            // $this->assertNotEmpty($faction['homesystem']);
            $this->assertNotEmpty($faction['name']);
            $this->assertNotEmpty($faction['wiki']);
        }
    }

    /**
     * Changing the name of a faction would break old drafts
     *
     * @return void
     */
    #[Test]
    public function allHistoricFactionsHaveData() {
        $historicFactions = json_decode(file_get_contents('data/historic-test-data/all-factions-ever.json'));

        $currentFactions = array_keys($this->getJsonData());

        foreach($historicFactions as $name) {
            $this->assertContains($name, $currentFactions);
        }
    }
}