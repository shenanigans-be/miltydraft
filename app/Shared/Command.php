<?php

declare(strict_types=1);

namespace App\Shared;

interface Command
{
    public function handle();
}