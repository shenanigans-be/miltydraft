<?php

declare(strict_types=1);

namespace App\Draft\Commands;

use App\Draft\Exceptions\InvalidDraftSettingsException;
use App\Draft\Slice;
use App\Shared\Command;
use App\Testing\Factories\DraftSettingsFactory;
use App\Testing\TestCase;
use App\Testing\TestSets;
use App\TwilightImperium\Edition;
use App\TwilightImperium\Tile;
use App\TwilightImperium\TileType;
use App\TwilightImperium\Wormhole;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;

class GenerateSlicePoolTest extends TestCase
{
    #[Test]
    public function itImplementsCommand(): void
    {
        $cmd = new GenerateSlicePool(DraftSettingsFactory::make());
        $this->assertInstanceOf(Command::class, $cmd);
    }

    #[Test]
    #[DataProviderExternal(TestSets::class, 'setCombinations')]
    public function itGathersTheCorrectTiles($sets): void
    {
        $settings = DraftSettingsFactory::make([
            'tileSets' => $sets,
        ]);
        $generator = new GenerateSlicePool($settings);

        $tiles = $generator->gatheredTiles();
        $tiers = $generator->gatheredTileTiers();
        $combinedTiers = count($tiers->allIds());

        $this->assertSame(count($tiles), $combinedTiers);
        foreach($tiles as $t) {
            $this->assertContains($t->edition, $sets);
            $this->assertNotEquals($t->tileType, TileType::GREEN);
            if (! empty($t->planets)) {
                $this->assertNotEquals($t->planets[0]->name, 'Mecatol Rex');
                $this->assertNotEquals($t->planets[0]->name, 'Mallice');
            }
        }
    }

    #[Test]
    #[DataProviderExternal(TestSets::class, 'setCombinations')]
    public function itCanGenerateValidSlicesBasedOnSets($sets): void
    {
        // we're doing this on easy mode so that the "Only base game" tile set has a decent chance of working
        $settings = DraftSettingsFactory::make([
            'numberOfSlices' => 4,
            'tileSets' => $sets,
            'maxOneWormholePerSlice' => false,
            'minimumLegendaryPlanets' => 0,
            'minimumTwoAlphaBetaWormholes' => false,
        ]);
        $generator = new GenerateSlicePool($settings);

        $slices = $generator->handle();

        $tileIds = array_reduce(
            $slices,
            fn ($allTiles, Slice $s) => array_merge(
                $allTiles,
                array_map(fn (Tile $t) => $t->id, $s->tiles),
            ),
            [],
        );

        $this->assertCount(4, $slices);
        foreach($slices as $slice) {
            $this->assertTrue($slice->validate(
                $settings->minimumOptimalInfluence,
                $settings->minimumOptimalResources,
                $settings->minimumOptimalTotal,
                $settings->maximumOptimalTotal,
                $settings->maxOneWormholesPerSlice,
            ));
        }
    }

    #[Test]
    #[DataProviderExternal(TestSets::class, 'setCombinations')]
    public function itDoesNotReuseTiles($sets): void
    {
        $settings = DraftSettingsFactory::make([
            'numberOfSlices' => 4,
            'tileSets' => [Edition::BASE_GAME, Edition::PROPHECY_OF_KINGS, Edition::THUNDERS_EDGE],
            'maxOneWormholePerSlice' => false,
            'minimumLegendaryPlanets' => 0,
            'minimumTwoAlphaBetaWormholes' => false,
        ]);
        $generator = new GenerateSlicePool($settings);

        $slices = $generator->handle();

        $tileIds = array_reduce(
            $slices,
            fn($allTiles, Slice $s) => array_merge(
                $allTiles,
                array_map(fn(Tile $t) => $t->id, $s->tiles),
            ),
            [],
        );

        $this->assertSameSize($tileIds, array_unique($tileIds));
    }

    #[Test]
    public function itGeneratesTheSameSlicesFromSameSeed(): void
    {
        $settings = DraftSettingsFactory::make([
            'seed' => 123,
            'numberOfSlices' => 4,
            'tileSets' => [Edition::BASE_GAME, Edition::PROPHECY_OF_KINGS],
            'maxOneWormholePerSlice' => true,
            'minimumLegendaryPlanets' => 1,
            'minimumTwoAlphaBetaWormholes' => true,
            'minimumOptimalInfluence' => 4,
            'minimumOptimalResources' => 2.5,
            'minimumOptimalTotal' => 9,
            'maximumOptimalTotal' => 13,
        ]);

        $generator = new GenerateSlicePool($settings);

        $slices = $generator->handle();

        $generatedSlices = array_map(fn (Slice $slice) => $slice->tileIds(), $slices);

        $secondGenerator = new GenerateSlicePool($settings);

        $slices = $secondGenerator->handle();

        foreach($slices as $sliceIndex => $slice) {
            $this->assertSame($generatedSlices[$sliceIndex], $slice->tileIds());
        }
    }

    #[Test]
    public function itCanGenerateSlicesForDifficultSettings(): void
    {
        $settings = DraftSettingsFactory::make([
            'numberOfSlices' => 8,
            'tileSets' => [Edition::BASE_GAME, Edition::PROPHECY_OF_KINGS, Edition::THUNDERS_EDGE],
            'maxOneWormholePerSlice' => true,
            'minimumLegendaryPlanets' => 2,
            'minimumTwoAlphaBetaWormholes' => true,
            'minimumOptimalTotal' => 10,
            'maximumOptimalTotal' => 13,
        ]);

        $generator = new GenerateSlicePool($settings);
        $generator->handle();

        $this->expectNotToPerformAssertions();
    }

    #[Test]
    public function itCanGenerateSlicesWithMinimumTwoAlphaAndBetaWormholes(): void
    {
        $settings = DraftSettingsFactory::make([
            'numberOfSlices' => 6,
            'tileSets' => [Edition::BASE_GAME, Edition::PROPHECY_OF_KINGS],
            'maxOneWormholePerSlice' => false,
            'minimumTwoAlphaBetaWormholes' => true,
        ]);

        $this->assertTrue($settings->minimumTwoAlphaAndBetaWormholes);

        $generator = new GenerateSlicePool($settings);

        $slices = $generator->handle();

        $alphaWormholeCount = 0;
        $betaWormholeCount = 0;
        foreach($slices as $slice) {
            if ($slice->hasWormhole(Wormhole::ALPHA)) {
                $alphaWormholeCount++;
            }
            if ($slice->hasWormhole(Wormhole::BETA)) {
                $betaWormholeCount++;
            }
        }

        $this->assertGreaterThanOrEqual(2, $alphaWormholeCount);
        $this->assertGreaterThanOrEqual(2, $betaWormholeCount);
    }

    #[Test]
    public function itCanGenerateSlicesWithMinimumAmountOfLegendaryPlanets(): void
    {
        $settings = DraftSettingsFactory::make([
            'numberOfSlices' => 6,
            'tileSets' => [Edition::BASE_GAME, Edition::PROPHECY_OF_KINGS],
            'minimumLegendaryPlanets' => 1,
        ]);
        $generator = new GenerateSlicePool($settings);

        $slices = $generator->handle();

        $legendaryPlanetCount = 0;
        foreach($slices as $slice) {
            if ($slice->hasLegendary()) {
                $legendaryPlanetCount++;
            }
        }

        $this->assertGreaterThanOrEqual(1, $legendaryPlanetCount);
    }

    #[Test]
    public function itCanGenerateSlicesWithMaxOneWormholePerSlice(): void
    {
        $settings = DraftSettingsFactory::make([
            'numberOfSlices' => 6,
            'tileSets' => [Edition::BASE_GAME, Edition::PROPHECY_OF_KINGS, Edition::DISCORDANT_STARS],
            'maxOneWormholePerSlice' => true,
        ]);
        $generator = new GenerateSlicePool($settings);

        $slices = $generator->handle();

        foreach($slices as $slice) {
            $this->assertLessThanOrEqual(1, count($slice->wormholes));
        }
    }

    #[Test]
    public function itCanReturnCustomSlices(): void
    {
        $customSlices = [
            ['64', '33', '42', '67', '59'],
            ['29', '66', '20', '39', '47'],
            ['27', '32', '79', '68', '19'],
            ['35', '37', '22', '40', '50'],
        ];

        $generator = new GenerateSlicePool(DraftSettingsFactory::make([
            'numberOfSlices' => 4,
            'customSlices' => $customSlices,
        ]));

        $slices = $generator->handle();

        foreach($slices as $sliceIndex => $slice) {
            $this->assertSame($customSlices[$sliceIndex], $slice->tileIds());
        }
    }

    #[Test]
    public function itGivesUpIfSettingsAreImpossible(): void
    {
        $generator = new GenerateSlicePool(DraftSettingsFactory::make([
            'numberOfSlices' => 4,
            'minimumOptimalInfluence' => 40,
        ]));

        $this->expectException(InvalidDraftSettingsException::class);
        $this->expectExceptionMessage(InvalidDraftSettingsException::cannotGenerateSlices()->getMessage());

        $generator->handle();
    }
}