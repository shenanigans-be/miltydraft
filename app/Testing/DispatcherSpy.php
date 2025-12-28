<?php

declare(strict_types=1);

namespace App\Testing;

use App\Shared\Command;

class DispatcherSpy
{
    public array $dispatchedCommands = [];

    public function __construct(
        public $commandReturnValue = null,
    ) {
    }

    public function handle(Command $command)
    {
        $this->dispatchedCommands[] = $command;

        return $this->commandReturnValue;
    }
}