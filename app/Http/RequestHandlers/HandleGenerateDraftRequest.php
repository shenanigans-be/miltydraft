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
use App\TwilightImperium\AllianceTeamMode;
use App\TwilightImperium\AllianceTeamPosition;
use App\TwilightImperium\Edition;

class HandleGenerateDraftRequest extends RequestHandler
{
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

        $customSlices = [];
        if ($this->request->get('custom_slices', '') != '') {
            $sliceData = explode("\n", $this->request->get('custom_slices'));
            foreach ($sliceData as $s) {
                $slice = [];
                $t = explode(',', $s);
                foreach ($t as $tile) {
                    $tile = trim($tile);
                    $slice[] = $tile;
                }
                $customSlices[] = $slice;
            }
        }

        return new Settings(
            $playerNames,
            $this->request->get('preset_draft_order') == 'on',
            new Name($this->request->get('game_name')),
            new Seed($this->request->get('seed') != null ? (int) $this->request->get('seed') : null),
            (int) $this->request->get('num_slices'),
            (int) $this->request->get('num_factions'),
            $this->tileSetsFromRequest(),
            $this->factionSetsFromRequest(),
            $this->request->get('include_keleres') == 'on',
            $this->request->get('wormholes', 0) == 1,
            $this->request->get('max_wormhole') == 'on',
            (int) $this->request->get('min_legendaries'),
            (float) $this->request->get('min_inf'),
            (float) $this->request->get('min_res'),
            (float) $this->request->get('min_total'),
            (float) $this->request->get('max_total'),
            $this->request->get('custom_factions') ?? [],
            $customSlices,
            $allianceMode,
            $allianceMode ? AllianceTeamMode::from($this->request->get('alliance_teams')) : null,
            $allianceMode ? AllianceTeamPosition::from($this->request->get('alliance_teams_position')) : null,
            $allianceMode ? $this->request->get('force_double_picks') == 'on' : null,
        );
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