<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\TwilightImperium\AllianceTeamMode;
use App\TwilightImperium\AllianceTeamPosition;

/**
 * Reads the draft options that both the generator and the settings editor submit.
 */
trait ReadsDraftSettings
{
    /**
     * @return array{presetDraftOrder: bool, numberOfSlices: int, numberOfFactions: int, includeCouncilKeleresFaction: bool, minimumTwoAlphaAndBetaWormholes: bool, maxOneWormholesPerSlice: bool, minimumLegendaryPlanets: int, minimumOptimalInfluence: float, minimumOptimalResources: float, minimumOptimalTotal: float, maximumOptimalTotal: float, customFactions: array<string>, customSlices: array<array<string>>} Named arguments for Settings
     */
    protected function draftOptionsFromRequest(): array
    {
        return [
            'presetDraftOrder' => $this->request->get('preset_draft_order') == 'on',
            'numberOfSlices' => (int) $this->request->get('num_slices'),
            'numberOfFactions' => (int) $this->request->get('num_factions'),
            'includeCouncilKeleresFaction' => $this->request->get('include_keleres') == 'on',
            'minimumTwoAlphaAndBetaWormholes' => $this->request->get('wormholes') == 'on',
            'maxOneWormholesPerSlice' => $this->request->get('max_wormhole') == 'on',
            'minimumLegendaryPlanets' => (int) $this->request->get('min_legendaries'),
            'minimumOptimalInfluence' => (float) $this->request->get('min_inf'),
            'minimumOptimalResources' => (float) $this->request->get('min_res'),
            'minimumOptimalTotal' => (float) $this->request->get('min_total'),
            'maximumOptimalTotal' => (float) $this->request->get('max_total'),
            'customFactions' => $this->request->get('custom_factions') ?? [],
            'customSlices' => $this->customSlicesFromRequest(),
        ];
    }

    /**
     * @return array{allianceTeamMode: ?AllianceTeamMode, allianceTeamPosition: ?AllianceTeamPosition, allianceForceDoublePicks: ?bool} Named arguments for Settings
     */
    protected function allianceOptionsFromRequest(bool $allianceMode): array
    {
        return [
            'allianceTeamMode' => $allianceMode ? AllianceTeamMode::from($this->request->get('alliance_teams')) : null,
            'allianceTeamPosition' => $allianceMode ? AllianceTeamPosition::from($this->request->get('alliance_teams_position')) : null,
            // the form's radio buttons send "true"/"false", checkboxes send "on"
            'allianceForceDoublePicks' => $allianceMode ? in_array($this->request->get('force_double_picks'), ['on', 'true'], true) : null,
        ];
    }

    /**
     * @return array<array<string>>
     */
    private function customSlicesFromRequest(): array
    {
        $customSlices = [];

        if ($this->request->get('custom_slices', '') != '') {
            $sliceData = explode("\n", $this->request->get('custom_slices'));
            foreach ($sliceData as $s) {
                if (trim($s) == '') {
                    continue;
                }

                $slice = [];
                $t = explode(',', $s);
                foreach ($t as $tile) {
                    $tile = ltrim(trim($tile), '0');
                    $slice[] = $tile;
                }
                $customSlices[] = $slice;
            }
        }

        return $customSlices;
    }
}
