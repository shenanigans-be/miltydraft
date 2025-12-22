<?php

namespace App\Draft\Repository;

use App\Draft\Draft;

interface DraftRepository
{
    public function load(string $id): Draft;
    public function save(Draft $draft);
}