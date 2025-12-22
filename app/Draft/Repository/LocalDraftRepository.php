<?php

namespace App\Draft\Repository;

use App\Draft\Draft;
use App\Draft\Exceptions\DraftRepositoryException;

class LocalDraftRepository implements DraftRepository
{
    private readonly string $storagePath;

    public function __construct()
    {
        $this->storagePath = env('STORAGE_PATH');
    }

    private function pathToDraft(string $draftId)
    {
        return $this->storagePath . '/' . 'draft_' . $draftId . '.json';
    }

    public function load(string $id): Draft
    {
        $path = $this->pathToDraft($id);

        if(!file_exists($path)) {
            throw DraftRepositoryException::notFound($id);
        }

        $rawDraft = file_get_contents($path);

        return Draft::fromJson($rawDraft);
    }

    public function save(Draft $draft)
    {
        file_put_contents($this->pathToDraft($draft->id), $draft->toFileContent());
    }
}