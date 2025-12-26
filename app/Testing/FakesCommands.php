<?php

namespace App\Testing;

use App\Shared\Command;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;

trait FakesCommands
{

    #[Before]
    public function setupSpy()
    {
        app()->spyOnDispatcher();
    }

    #[After]
    public function teardownSpy()
    {
        app()->dontSpyOnDispatcher();
    }

    public function setExpectedReturnValue($return = null)
    {
        app()->spyOnDispatcher($return);
    }

    public function assertCommandWasDispatched($class, $times = 1)
    {
        $dispatched = array_filter(
            app()->spy->dispatchedCommands,
            fn (Command $cmd) => get_class($cmd) == $class
        );
        Assert::assertSame($times, count($dispatched));
    }

    public function assertCommandWasDispatchedWith($class, $callback, $times = 1)
    {
        $dispatched = array_filter(
            app()->spy->dispatchedCommands,
            $callback
        );
    }
}