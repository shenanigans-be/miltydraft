<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Commands\UpdateDraftSettings;
use App\Draft\Exceptions\InvalidDraftSettingsException;
use App\Draft\Pick;
use App\Draft\PickCategory;
use App\Draft\PlayerId;
use App\Testing\FakesCommands;
use App\Testing\RequestHandlerTestCase;
use App\Testing\UsesTestDraft;
use PHPUnit\Framework\Attributes\Test;

class HandleUpdateSettingsRequestTest extends RequestHandlerTestCase
{
    use UsesTestDraft;
    use FakesCommands;

    protected string $requestHandlerClass = HandleUpdateSettingsRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler(): void
    {
        $this->assertIsConfiguredAsHandlerForRoute('/api/settings');
    }

    #[Test]
    public function itReturnsErrorIfDraftNotFound(): void
    {
        $response = $this->handleRequest(['id' => '12344']);

        $this->assertResponseNotFound($response);
        $this->assertResponseJson($response);
        $this->assertJsonResponseSame(['error' => 'Draft not found'], $response);
    }

    #[Test]
    public function itReturnsErrorIfNotAdmin(): void
    {
        $response = $this->handleRequest(['id' => $this->testDraft->id, 'admin' => 'not-the-correct-secret']);
        $this->assertForbidden($response);
    }

    #[Test]
    public function itReturnsErrorIfDraftHasStarted(): void
    {
        $this->testDraft->log[] = new Pick(
            PlayerId::fromString(array_key_first($this->testDraft->players)),
            PickCategory::FACTION,
            'The Mentak Coalition',
        );
        app()->repository->save($this->testDraft);

        $response = $this->handleRequest($this->validPayload());

        $this->assertResponseCode(400, $response);
        $this->assertJsonResponseSame(['error' => 'Settings can only be changed before the draft has started'], $response);
    }

    #[Test]
    public function itReturnsErrorWhenSettingsAreInvalid(): void
    {
        $payload = $this->validPayload();
        $payload['player_names'] = array_fill_keys(array_keys($this->testDraft->players), 'Same name');

        $response = $this->handleRequest($payload);

        $this->assertResponseCode(400, $response);
        $this->assertJsonResponseSame(['error' => InvalidDraftSettingsException::playerNamesNotUnique()->getMessage()], $response);
    }

    #[Test]
    public function itDispatchesTheCommand(): void
    {
        $response = $this->handleRequest($this->validPayload());

        $this->assertResponseOk($response);
        $this->assertResponseJson($response);
        $this->assertCommandWasDispatched(UpdateDraftSettings::class);
    }

    #[Test]
    public function itPassesTheSubmittedNamesAndSettingsToTheCommand(): void
    {
        $payload = $this->validPayload();
        $ids = array_keys($this->testDraft->players);
        $payload['player_names'][$ids[0]] = '  Renamed  ';
        $payload['num_slices'] = '8';

        $this->handleRequest($payload);

        /** @var UpdateDraftSettings $cmd */
        $cmd = app()->spy->dispatchedCommands[0];
        $this->assertSame('Renamed', $cmd->playerNames[$ids[0]]);
        $this->assertSame(8, $cmd->settings->numberOfSlices);
        $this->assertSame($this->testDraft->settings->seed->getValue(), $cmd->settings->seed->getValue());
        $this->assertSame($this->testDraft->settings->tileSets, $cmd->settings->tileSets);
        $this->assertSame($this->testDraft->settings->factionSets, $cmd->settings->factionSets);
    }

    private function validPayload(): array
    {
        $playerNames = [];
        foreach ($this->testDraft->players as $id => $player) {
            $playerNames[$id] = $player->name;
        }

        return [
            'id' => $this->testDraft->id,
            'admin' => $this->testDraft->secrets->adminSecret,
            'player_names' => $playerNames,
            'num_slices' => '7',
            'num_factions' => '9',
            'min_inf' => '4',
            'min_res' => '2.5',
            'min_total' => '9',
            'max_total' => '13',
        ];
    }
}
