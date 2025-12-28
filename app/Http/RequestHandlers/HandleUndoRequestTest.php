<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Commands\UndoLastPick;
use App\Testing\FakesCommands;
use App\Testing\RequestHandlerTestCase;
use App\Testing\UsesTestDraft;
use PHPUnit\Framework\Attributes\Test;

class HandleUndoRequestTest extends RequestHandlerTestCase
{
    use UsesTestDraft;
    use FakesCommands;

    protected string $requestHandlerClass = HandleUndoRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler(): void
    {
        $this->assertIsConfiguredAsHandlerForRoute('/api/undo');
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
    public function itDispatchesTheCommand(): void
    {
        $response = $this->handleRequest(['id' => $this->testDraft->id, 'admin' => $this->testDraft->secrets->adminSecret]);
        $this->assertResponseOk($response);
        $this->assertResponseJson($response);
        $this->assertCommandWasDispatched(UndoLastPick::class);
    }
}