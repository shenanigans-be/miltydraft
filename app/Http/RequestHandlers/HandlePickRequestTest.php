<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Commands\ClaimPlayer;
use App\Draft\Commands\PlayerPick;
use App\Draft\PickCategory;
use App\Draft\PlayerId;
use App\Testing\FakesCommands;
use App\Testing\RequestHandlerTestCase;
use App\Testing\UsesTestDraft;
use PHPUnit\Framework\Attributes\Test;

class HandlePickRequestTest extends RequestHandlerTestCase
{
    use UsesTestDraft;
    use FakesCommands;

    protected string $requestHandlerClass = HandlePickRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler(): void
    {
        $this->assertIsConfiguredAsHandlerForRoute('/api/pick');
    }

    #[Test]
    public function itReturnsErrorIfDraftNotFound(): void
    {
        $response = $this->handleRequest(['id' => '12344']);

        $this->assertSame(404, $response->code);
        $this->assertResponseJson($response);
        $this->assertJsonResponseSame(['error' => 'Draft not found'], $response);
    }

    #[Test]
    public function itReturnsErrorIfPlayerSecretIsIncorrect(): void
    {
        $playerId = PlayerId::fromString(array_keys($this->testDraft->players)[0]);

        $response = $this->handleRequest([
            'id' => $this->testDraft->id,
            'player' => $playerId->value,
            'secret' => 'incorrect-value',
            'category' => PickCategory::SLICE->value,
            'value' => '3',
        ]);

        $this->assertForbidden($response);
    }

    #[Test]
    public function itAllowsAdminsToMakePicksForOtherPlayers(): void
    {
        $playerId = PlayerId::fromString(array_keys($this->testDraft->players)[0]);

        $response = $this->handleRequest([
            'id' => $this->testDraft->id,
            'player' => $playerId->value,
            'admin' => $this->testDraft->secrets->adminSecret,
            'category' => PickCategory::SLICE->value,
            'value' => '3',
        ]);

        $this->assertResponseOk($response);
    }

    #[Test]
    public function itReturnsErrorIfIndexIsIncorrect(): void
    {
        $playerId = PlayerId::fromString(array_keys($this->testDraft->players)[0]);

        $response = $this->handleRequest([
            'id' => $this->testDraft->id,
            'index' => 4,
            'player' => $playerId->value,
            'admin' => $this->testDraft->secrets->adminSecret,
            'category' => PickCategory::FACTION->value,
            'value' => 'Hacan',
        ]);

        $this->assertResponseCode(400, $response);
        $this->assertJsonResponseSame([
            'error' => 'Draft data out of date, meaning: stuff has been picked or undone while this tab was open.',
        ], $response);
    }

    #[Test]
    public function itDispatchesTheCommand(): void
    {
        $playerId = PlayerId::fromString(array_keys($this->testDraft->players)[0]);

        $secret = (new ClaimPlayer($this->testDraft, $playerId))->handle();

        $category = PickCategory::FACTION->value;
        $value = 'Hacan';

        $response = $this->handleRequest([
            'id' => $this->testDraft->id,
            'index' => 0,
            'player' => $playerId->value,
            'secret' => $secret,
            'category' => $category,
            'value' => $value,
        ]);

        $this->assertResponseOk($response);
        $this->assertResponseJson($response);

        $this->assertCommandWasDispatchedWith(
            PlayerPick::class,
            function (PlayerPick $cmd) use ($playerId, $category, $value): void {
                $this->assertSame($cmd->draft->id, $this->testDraft->id);
                $this->assertSame($cmd->pick->playerId->value, $playerId->value);
                $this->assertSame($cmd->pick->category->value, $category);
                $this->assertSame($cmd->pick->pickedOption, $value);
            },
        );
    }
}