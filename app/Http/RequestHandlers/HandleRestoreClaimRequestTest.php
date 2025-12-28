<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Commands\ClaimPlayer;
use App\Draft\PlayerId;
use App\Testing\RequestHandlerTestCase;
use App\Testing\UsesTestDraft;
use PHPUnit\Framework\Attributes\Test;

class HandleRestoreClaimRequestTest extends RequestHandlerTestCase
{
    use UsesTestDraft;

    protected string $requestHandlerClass = HandleRestoreClaimRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler(): void
    {
        $this->assertIsConfiguredAsHandlerForRoute('/api/restore');
    }

    #[Test]
    public function itReturnsJson(): void
    {
        $response = $this->handleRequest(['draft' => $this->testDraft->id, 'secret' => $this->testDraft->secrets->adminSecret]);
        $this->assertResponseOk($response);
        $this->assertResponseJson($response);
    }

    #[Test]
    public function itCanRestoreAnAdminClaim(): void
    {
        $adminSecret = $this->testDraft->secrets->adminSecret;
        $response = $this->handleRequest(['draft' => $this->testDraft->id, 'secret' => $adminSecret]);
        $this->assertResponseOk($response);
        $this->assertJsonResponseSame([
            'admin' => $adminSecret,
            'success' => true,
        ], $response);
    }

    #[Test]
    public function itCanRestoreAPlayerClaim(): void
    {
        $playerId = array_keys($this->testDraft->players)[0];

        (new ClaimPlayer($this->testDraft, PlayerId::fromString($playerId)))->handle();

        $playerSecret = $this->testDraft->secrets->playerSecrets[$playerId];
        $response = $this->handleRequest(['draft' => $this->testDraft->id, 'secret' => $playerSecret]);
        $this->assertResponseOk($response);
        $this->assertJsonResponseSame([
            'player' => $playerId,
            'secret' => $playerSecret,
            'success' => true,
        ], $response);
    }

    #[Test]
    public function itThrowsAnErrorIfDraftDoesntExist(): void
    {
        $response = $this->handleRequest(['draft' => '1234', 'secret' => 'blabla']);
        $this->assertResponseNotFound($response);
    }

    #[Test]
    public function itThrowsAnErrorIfNoPlayerWasFound(): void
    {
        $response = $this->handleRequest(['draft' => $this->testDraft->id, 'secret' => 'blabla']);
        $this->assertForbidden($response);
    }
}