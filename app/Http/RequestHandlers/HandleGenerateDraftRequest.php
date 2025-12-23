<?php

namespace App\Http\RequestHandlers;

use App\Draft\Generators\DraftGenerator;
use App\Draft\Settings;
use App\Http\HttpResponse;
use App\Http\RequestHandler;

class HandleGenerateDraftRequest extends RequestHandler
{
    public function handle(): HttpResponse
    {
        $settings = Settings::fromRequest($this->request);
        dd($settings);

        $draft = (new DraftGenerator($settings))->generate();

        app()->repository->save($draft);

        return $this->json([
            'id' => $draft->id,
            'admin' => $draft->secrets->adminSecret
        ]);
    }
}