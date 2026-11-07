<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Commands\GenerateDraft;
use App\Draft\Exceptions\InvalidDraftSettingsException;
use App\Draft\Name;
use App\Draft\Seed;
use App\Draft\Settings;
use App\Http\HttpRequest;
use App\Http\HttpResponse;
use App\Http\RequestHandler;
use App\TwilightImperium\Edition;

class HandleGenerateDraftRequest extends RequestHandler
{
    use ReadsDraftSettings;

    private Settings $settings;

    public function __construct(HttpRequest $request)
    {
        parent::__construct($request);
        // parse settings from request
        $this->settings = $this->settingsFromRequest();
    }

    public function handle(): HttpResponse
    {
        try {
            $this->settings->validate();
        } catch (InvalidDraftSettingsException $e) {
            return $this->error($e->getMessage(), 400);
        }

        $draft = dispatch(new GenerateDraft($this->settingsFromRequest()));

        app()->repository->save($draft);

        return $this->json([
            'id' => $draft->id,
            'admin' => $draft->secrets->adminSecret,
        ]);
    }

    private function settingsFromRequest(): Settings
    {
        $playerNames = [];
        for ($i = 0; $i < $this->request->get('num_players'); $i++) {
            $playerNames[] = trim($this->request->get('player')[$i] ?? '');
        }

        $allianceMode = (bool) $this->request->get('alliance_on', false);

        return new Settings(...[
            'playerNames' => $playerNames,
            'name' => new Name($this->request->get('game_name')),
            'seed' => new Seed($this->request->get('seed') != null ? (int) $this->request->get('seed') : null),
            'tileSets' => $this->tileSetsFromRequest(),
            'factionSets' => $this->factionSetsFromRequest(),
            'allianceMode' => $allianceMode,
            ...$this->draftOptionsFromRequest(),
            ...$this->allianceOptionsFromRequest($allianceMode),
        ]);
    }

    protected function tileSetsFromRequest()
    {
        $sets = [Edition::BASE_GAME];
        foreach($this->request->get('tileSets', []) as $key => $value) {
            $edition = Edition::from($key);
            if ($value == 'on' && ! in_array($edition, $sets)) {
                $sets[] = $edition;
            }
        }

        return $sets;
    }

    protected function factionSetsFromRequest()
    {
        $sets = [];
        foreach($this->request->get('factionSets', []) as $key => $value) {
            if ($value == 'on') {
                $sets[] = Edition::from($key);
            }
        }

        return $sets;
    }

    /** used for tests */
    public function settingValue($field) {
        return $this->settings->$field;
    }
}