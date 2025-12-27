<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Commands\RegenerateDraft;
use App\Testing\FakesCommands;
use App\Testing\RequestHandlerTestCase;
use App\Testing\UsesTestDraft;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class HandleRegenerateDraftRequestTest extends RequestHandlerTestCase
{
    use UsesTestDraft;
    use FakesCommands;

    protected string $requestHandlerClass = HandleRegenerateDraftRequest::class;

    #[Test]
    public function itIsConfiguredAsRouteHandler(): void
    {
        $this->assertIsConfiguredAsHandlerForRoute('/api/regenerate');
    }

    #[Test]
    public function itReturnsErrorIfDraftNotFound(): void
    {
        $response = $this->handleRequest(['id' => '12344', 'secret' => '']);

        $this->assertResponseNotFound($response);
        $this->assertResponseJson($response);
        $this->assertJsonResponseSame(['error' => 'Draft not found'], $response);
    }

    #[Test]
    public function itReturnsErrorIfNotAdmin(): void
    {
        $response = $this->handleRequest(['id' => $this->testDraft->id, 'secret' => 'Not admin']);
        $this->assertForbidden($response);
    }

    public static function parameters()
    {
        yield 'When regenerating slices' => [
            'slices' => 'true',
            'factions' => 'false',
            'order' => 'false',
        ];

        yield 'When regenerating factions' => [
            'slices' => 'false',
            'factions' => 'false',
            'order' => 'true',
        ];

        yield 'When regenerating player order' => [
            'slices' => 'false',
            'factions' => 'false',
            'order' => 'true',
        ];
    }

    #[Test]
    #[DataProvider('parameters')]
    public function itDispatchesTheCommand($slices, $factions, $order): void
    {
        $response = $this->handleRequest([
            'id' => $this->testDraft->id,
            'slices' => $slices,
            'factions' => $factions,
            'order' => $order,
            'admin' => $this->testDraft->secrets->adminSecret,
        ]);

        $this->assertCommandWasDispatchedWith(
            RegenerateDraft::class,
            function (RegenerateDraft $cmd) use ($slices, $factions, $order): void {
                $this->assertSame($cmd->regenerateSlices, $slices == 'true');
                $this->assertSame($cmd->regenerateFactions, $factions == 'true');
                $this->assertSame($cmd->regenerateOrder, $order == 'true');
            },
        );
    }
}