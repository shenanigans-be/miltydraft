<?php

namespace App\Testing;

use App\Draft\Draft;
use App\Draft\Generators\DraftGenerator;
use App\Testing\Factories\DraftSettingsFactory;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;

trait UsesTestDraft
{
    protected Draft $testDraft;

    #[Before]
    protected function setupTestDraft($settings = null)
    {
        if ($settings == null) {
            $settings = DraftSettingsFactory::make();
        }

        $this->testDraft = (new DraftGenerator($settings))->generate();
        app()->repository->save($this->testDraft);
    }


    #[After]
    public function deleteTestDraft()
    {
        app()->repository->delete($this->testDraft->id);
        unset($this->testDraft);
    }
}