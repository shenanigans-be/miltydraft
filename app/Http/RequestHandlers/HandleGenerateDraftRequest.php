<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Commands\GenerateDraft;
use App\Draft\Exceptions\InvalidDraftSettingsException;
use App\Draft\Name;
use App\Draft\Seed;
use App\Draft\Settings;
use App\Http\ErrorResponse;
use App\Http\HttpRequest;
use App\Http\HttpResponse;
use App\Http\RequestHandler;
use App\TwilightImperium\AllianceTeamMode;
use App\TwilightImperium\AllianceTeamPosition;

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
            return new ErrorResponse($e->getMessage(), 400);
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
            new Name($this->request->get('name')),
            new Seed($this->request->get('seed') != null ? (int) $this->request->get('seed') : null),
            (int) $this->request->get('num_slices'),
            (int) $this->request->get('num_factions'),
            Settings::tileSetsFromPayload([
                'include_pok' => $this->request->get('include_pok') == 'on',
                'include_ds_tiles' => $this->request->get('include_ds_tiles') == 'on',
                'include_te_tiles' => $this->request->get('include_te_tiles') == 'on',
            ]),
            Settings::factionSetsFromPayload([
                'include_base_factions' => $this->request->get('include_base_factions') == 'on',
                'include_pok_factions' => $this->request->get('include_pok_factions') == 'on',
                'include_te_factions' => $this->request->get('include_te_factions') == 'on',
                'include_discordant' => $this->request->get('include_discordant') == 'on',
                'include_discordantexp' => $this->request->get('include_discordantexp') == 'on',
            ]),
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

    /** used for tests */
    public function settingValue($field) {
        return $this->settings->$field;
    }
}