<?php

declare(strict_types=1);

namespace App\Testing;

use App\Draft\Commands\GenerateDraft;
use App\Draft\Draft;
use App\Testing\Factories\DraftSettingsFactory;
use App\TwilightImperium\Edition;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;

trait UsesTestDraft
{
    protected Draft $testDraft;

    #[Before]
    protected function setupTestDraft($settings = null): void
    {
        if ($settings == null) {
            // make a standard-ass draft. We should test edge cases separately
            $settings = DraftSettingsFactory::make([
                'num_players' => 6,
                'tileSets' => [Edition::BASE_GAME, Edition::PROPHECY_OF_KINGS, Edition::THUNDERS_EDGE],
                'factionSets' => [Edition::BASE_GAME, Edition::PROPHECY_OF_KINGS, Edition::THUNDERS_EDGE],
                'minimumTwoAlphaBetaWormholes' => false,
                'minimumLegendaryPlanets' => 0,
                'maxOneWormholePerSlice' => true,
            ]);
        }

        $this->testDraft = (new GenerateDraft($settings))->handle();
        app()->repository->save($this->testDraft);
    }

    public function reloadDraft(): void
    {
        $this->testDraft = app()->repository->load($this->testDraft->id);
    }

    #[After]
    public function deleteTestDraft(): void
    {
        app()->repository->delete($this->testDraft->id);
        unset($this->testDraft);
    }
}