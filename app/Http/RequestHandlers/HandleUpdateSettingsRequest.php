<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Commands\UpdateDraftSettings;
use App\Draft\Draft;
use App\Draft\Exceptions\InvalidDraftSettingsException;
use App\Draft\Settings;
use App\Http\HttpResponse;

class HandleUpdateSettingsRequest extends DraftRequestHandler
{
    use ReadsDraftSettings;

    public function handle(): HttpResponse
    {
        $draft = $this->loadDraftByUrlId();

        if ($draft == null) {
            return $this->error('Draft not found', 404);
        }

        $adminSecret = $this->request->get('admin');

        if (! $draft->secrets->checkAdminSecret($adminSecret)) {
            return $this->error('Only the admin can change the settings', 403);
        }

        if (! $draft->canRegenerate()) {
            return $this->error('Settings can only be changed before the draft has started', 400);
        }

        $playerNames = $this->playerNamesFromRequest($draft);

        $settings = new Settings(...[
            'playerNames' => array_values($playerNames),
            'name' => $draft->settings->name,
            'seed' => $draft->settings->seed,
            'tileSets' => $draft->settings->tileSets,
            'factionSets' => $draft->settings->factionSets,
            'allianceMode' => $draft->settings->allianceMode,
            ...$this->draftOptionsFromRequest(),
            ...$this->allianceOptionsFromRequest($draft->settings->allianceMode),
        ]);

        try {
            $settings->validate();
        } catch (InvalidDraftSettingsException $e) {
            return $this->error($e->getMessage(), 400);
        }

        dispatch(new UpdateDraftSettings($draft, $settings, $playerNames));

        return $this->json([
            'draft' => $draft->toArray(),
            'success' => true,
        ]);
    }

    /**
     * @return array<string, string> Keyed by player id, in draft order
     */
    private function playerNamesFromRequest(Draft $draft): array
    {
        $submitted = $this->request->get('player_names', []);
        $playerNames = [];

        foreach ($draft->players as $id => $player) {
            $playerNames[$id] = trim($submitted[$id] ?? $player->name);
        }

        return $playerNames;
    }
}
