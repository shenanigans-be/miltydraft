<?php

namespace App\Draft\Commands;

use App\Testing\Factories\DraftSettingsFactory;
use App\Testing\TestCase;
use App\Testing\TestSets;
use App\TwilightImperium\Edition;
use App\TwilightImperium\Faction;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

class GenerateFactionPoolTest extends TestCase
{
    #[Test]
    #[DataProviderExternal(TestSets::class, 'setCombinations')]
    public function itCanGenerateChoicesFromFactionSets($sets)
    {
        $generator = new GenerateFactionPool(DraftSettingsFactory::make([
            'factionSets' => $sets,
            'numberOfFactions' => 10
        ]));

        $choices = $generator->handle();
        $choicesNames = array_map(fn (Faction $faction) => $faction->name, $choices);

        $this->assertCount(10, $choices);
        $this->assertCount(10, array_unique($choicesNames));
        foreach($choices as $choice) {
            $this->assertContains($choice->edition, $sets);
        }
    }

    #[Test]
    public function itUsesOnlyCustomFactionsWhenEnoughAreProvided()
    {
        $customFactions = [
            "The Barony of Letnev",
            "The Clan of Saar",
            "The Emirates of Hacan",
            "The Ghosts of Creuss",
        ];
        $generator = new GenerateFactionPool(DraftSettingsFactory::make([
            'customFactions' => $customFactions,
            'factionSets' => [Edition::BASE_GAME],
            'numberOfFactions' => 3
        ]));

        $choices = $generator->handle();

        $this->assertCount(3, $choices);
        foreach($choices as $choice) {
            $this->assertContains($choice->name, $customFactions);
        }
    }

    #[Test]
    public function itGeneratesTheSameFactionsFromTheSameSeed()
    {
        $generator = new GenerateFactionPool(DraftSettingsFactory::make([
            'seed' => 123,
            'factionSets' => [Edition::BASE_GAME],
            'numberOfFactions' => 3
        ]));
        $previouslyGeneratedChoices = [
            'The Ghosts of Creuss',
            'The Emirates of Hacan',
            'The Yssaril Tribes'
        ];

        $choices = $generator->handle();

        foreach($previouslyGeneratedChoices as $i => $name) {
            $this->assertSame($name, $choices[$i]->name);
        }
    }

    #[Test]
    public function itTakesFromSetsWhenNotEnoughCustomFactionsAreProvided()
    {
        $customFactions = [
            'The Ghosts of Creuss',
            'The Emirates of Hacan',
            'The Yssaril Tribes'
        ];
        $generator = new GenerateFactionPool(DraftSettingsFactory::make([
            'factionSets' => [Edition::BASE_GAME],
            'customFactions' => $customFactions,
            'numberOfFactions' => 10
        ]));

        $choices = $generator->handle();
        $choicesNames = array_map(fn (Faction $faction) => $faction->name, $choices);

        foreach($customFactions as $f) {
            $this->assertContains($f, $choicesNames);
        }

        foreach($choices as $c) {
            $this->assertEquals($c->edition, Edition::BASE_GAME);
        }
    }
}