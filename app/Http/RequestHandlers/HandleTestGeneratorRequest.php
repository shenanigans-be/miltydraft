<?php

namespace App\Http\RequestHandlers;

use App\Draft\Generators\DraftGenerator;
use App\Http\HttpResponse;
use App\Http\RequestHandler;
use App\Testing\Factories\DraftSettingsFactory;

class HandleTestGeneratorRequest extends RequestHandler
{
    public function handle(): HttpResponse
    {
        $settings = DraftSettingsFactory::make();
        $generator = new DraftGenerator($settings);

        $generator->generate();

        return $this->json([
            'settings' => $settings->toArray()
        ]);
    }
}