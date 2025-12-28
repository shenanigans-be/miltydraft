<?php

declare(strict_types=1);

namespace App\Http\RequestHandlers;

use App\Draft\Draft;
use App\Draft\Exceptions\DraftRepositoryException;
use App\Http\RequestHandler;

abstract class DraftRequestHandler extends RequestHandler
{
    public function loadDraftByUrlId($urlKey = 'id'): ?Draft
    {
        try {
            return  app()->repository->load($this->request->get($urlKey));
        } catch (DraftRepositoryException $e) {
            return null;
        }
    }
}