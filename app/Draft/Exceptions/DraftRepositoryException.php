<?php

declare(strict_types=1);

namespace App\Draft\Exceptions;

class DraftRepositoryException extends \Exception
{
    public static function notFound(string $id)
    {
        return new self('Draft with ' . $id . ' was not found using current storage');
    }
}