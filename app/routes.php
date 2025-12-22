<?php

return [
    '/' => \App\Http\RequestHandlers\HandleViewFormRequest::class,
    '/d/{id}' => \App\Http\RequestHandlers\HandleViewDraftRequest::class,
    '/api/generate' => \App\Http\RequestHandlers\HandleGenerateDraftRequest::class,
    '/api/draft/{id}/regenerate' => \App\Http\RequestHandlers\HandleGenerateDraftRequest::class,
    '/api/draft/{id}/pick' => \App\Http\RequestHandlers\HandlePickRequest::class,
    '/api/draft/{id}/claim' => \App\Http\RequestHandlers\HandleClaimPlayerRequest::class,
    '/api/draft/{id}/restore' => \App\Http\RequestHandlers\HandleRestoreClaimRequest::class,
    '/api/draft/{id}/undo' => \App\Http\RequestHandlers\HandleUndoRequest::class,
    '/api/data/{id}' => \App\Http\RequestHandlers\HandleGetDraftRequest::class,
];
