<?php

namespace data;

use App\TwilightImperium\Planet;
use App\TwilightImperium\SpaceStation;
use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class FactionDataTest extends TestCase
{
    protected static function getJsonData(): array
    {
        return json_decode(file_get_contents('data/factions.json'), true);
    }

    public static function allJsonFactions(): iterable
    {
        $data = self::getJsonData();
        foreach($data as $key => $factionData) {
            yield 'For Faction ' . $factionData['name'] => [
                'key' => $key,
                'factionData' => $factionData
            ];
        }
    }

    #[Test]
    #[DataProvider('allJsonFactions')]
    public function eachFactionHasData($key, $factionData) {
        $this->assertNotEmpty($factionData['set']);
        if ($key != 'The Council Keleres') {
            $this->assertNotEmpty($factionData['homesystem']);
        }
        $this->assertNotEmpty($factionData['name']);
        $this->assertNotEmpty($factionData['wiki']);
    }


    #[Test]
    #[DataProvider('allJsonFactions')]
    public function eachFactionHasNameAsKey($key, $factionData) {
        $this->assertSame($factionData['name'], $key);
    }

    /**
     * Changing the name of a faction would break old drafts
     *
     * @return void
     */
    #[Test]
    public function allHistoricFactionsHaveData() {
        $historicFactions = json_decode(file_get_contents('data/historic-test-data/all-factions-ever.json'));

        $currentFactions = array_keys(self::getJsonData());

        foreach($historicFactions as $name) {
            $this->assertContains($name, $currentFactions);
        }
    }
}