<?php

namespace App;

use App\Http\RequestHandlers\HandleClaimPlayerRequest;
use App\Http\RequestHandlers\HandleGenerateDraftRequest;
use App\Http\RequestHandlers\HandleGetDraftRequest;
use App\Http\RequestHandlers\HandlePickRequest;
use App\Http\RequestHandlers\HandleRegenerateDraftRequest;
use App\Http\RequestHandlers\HandleRestoreClaimRequest;
use App\Http\RequestHandlers\HandleUndoRequest;
use App\Http\RequestHandlers\HandleViewDraftRequest;
use App\Http\RequestHandlers\HandleViewFormRequest;
use App\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * @todo: fix trailing slash
 */
class ApplicationTest extends TestCase
{
    public static function allRoutes()
    {
        yield "For viewing the form" => [
            'route' => '/',
            'handler' => HandleViewFormRequest::class
        ];

        yield "For viewing a draft" => [
            'route' => '/d/1234',
            'handler' => HandleViewDraftRequest::class
        ];

        yield "For fetching draft data" => [
            'route' => '/api/data/1234',
            'handler' => HandleGetDraftRequest::class
        ];

        yield "For generating a draft" => [
            'route' => '/api/generate',
            'handler' => HandleGenerateDraftRequest::class
        ];

        yield "For making a pick" => [
            'route' => '/api/draft/1234/pick',
            'handler' => HandlePickRequest::class
        ];

        yield "For claiming a player" => [
            'route' => '/api/draft/1234/claim',
            'handler' => HandleClaimPlayerRequest::class
        ];

        yield "For restoring a claim" => [
            'route' => '/api/draft/1234/restore',
            'handler' => HandleRestoreClaimRequest::class
        ];

        yield "For undoing a pick" => [
            'route' => '/api/draft/1234/undo',
            'handler' => HandleUndoRequest::class
        ];

        yield "For regenerating a draft" => [
            'route' => '/api/draft/1234/regenerate',
            'handler' => HandleRegenerateDraftRequest::class
        ];
    }

    #[Test]
    #[DataProvider('allRoutes')]
    public function itHasHandlerForAllRoutes($route, $handler)
    {
        $application = new Application();
        $determinedHandler = $application->handlerForRequest($route);

        $this->assertInstanceOf($handler, $determinedHandler);
    }
}