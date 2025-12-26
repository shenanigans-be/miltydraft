<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Commands\ClaimPlayer;
use App\Draft\Commands\UnclaimPlayer;
use App\Testing\FakesCommands;
use App\Testing\RequestHandlerTestCase;
use App\Testing\UsesTestDraft;
use PHPUnit\Framework\Attributes\Test;

class HandleClaimOrUnclaimPlayerRequestTest extends RequestHandlerTestCase
{
    use UsesTestDraft;
    use FakesCommands;
    protected string $requestHandlerClass = HandleClaimOrUnclaimPlayerRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler(): void
    {
        $this->assertIsConfiguredAsHandlerForRoute('/api/claim');
    }

    #[Test]
    public function itReturnsJson(): void
    {
        $response = $this->handleRequest(['draft' => $this->testDraft->id, 'player' => array_keys($this->testDraft->players)[0]]);
        $this->assertResponseOk($response);
        $this->assertResponseJson($response);
    }

    #[Test]
    public function itReturnsErrorIfDraftNotFound(): void
    {

        $response = $this->handleRequest(['draft' => '123', 'player' => '123']);
        $this->assertResponseNotFound($response);
        $this->assertResponseJson($response);
        $this->assertJsonResponseSame(['error' => 'Draft not found'], $response);
    }

    #[Test]
    public function itCanClaimAPlayer(): void
    {
        $this->setExpectedReturnValue('1234');
        $playerId = array_keys($this->testDraft->players)[0];
        $this->handleRequest(['draft' => $this->testDraft->id, 'player' => $playerId]);
        $this->assertCommandWasDispatchedWith(ClaimPlayer::class, function (ClaimPlayer $cmd) use ($playerId): void {
            $this->assertSame($this->testDraft->id, $cmd->draft->id);
            $this->assertSame($playerId, $cmd->playerId->value);
        });
    }

    #[Test]
    public function itCanUnclaimAPlayer(): void
    {
        $playerId = array_keys($this->testDraft->players)[0];
        $this->handleRequest(['draft' => $this->testDraft->id, 'player' => $playerId, 'unclaim' => 1]);
        $this->assertCommandWasDispatchedWith(UnclaimPlayer::class, function (UnclaimPlayer $cmd) use ($playerId): void {
            $this->assertSame($this->testDraft->id, $cmd->draft->id);
            $this->assertSame($playerId, $cmd->playerId->value);
        });
    }
}