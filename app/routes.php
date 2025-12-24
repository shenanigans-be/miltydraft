<?php

return [
    '/' => \App\Http\RequestHandlers\HandleViewFormRequest::class,
    '/d/{id}' => \App\Http\RequestHandlers\HandleViewDraftRequest::class,
    '/api/generate' => \App\Http\RequestHandlers\HandleGenerateDraftRequest::class,
    '/api/regenerate' => \App\Http\RequestHandlers\HandleRegenerateDraftRequest::class,
    '/api/pick' => \App\Http\RequestHandlers\HandlePickRequest::class,
    '/api/claim' => \App\Http\RequestHandlers\HandleClaimPlayerRequest::class,
    '/api/restore' => \App\Http\RequestHandlers\HandleRestoreClaimRequest::class,
    '/api/undo' => \App\Http\RequestHandlers\HandleUndoRequest::class,
    '/api/draft/{id}' => \App\Http\RequestHandlers\HandleGetDraftRequest::class,
    '/test' => \App\Http\RequestHandlers\HandleTestGeneratorRequest::class,
];
