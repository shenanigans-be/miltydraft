<?php

namespace App\Testing;

use App\Shared\Command;
use PHPUnit\Framework\Assert;

class DispatcherSpy
{
    public array $dispatchedCommands = [];

    public function __construct(
        public $commandReturnValue = null
    ) {
    }

    public function handle(Command $command)
    {
        $this->dispatchedCommands[] = $command;
        return $this->commandReturnValue;
    }
}