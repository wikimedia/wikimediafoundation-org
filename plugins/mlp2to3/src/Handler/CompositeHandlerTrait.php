<?php

namespace Inpsyde\MultilingualPress2to3\Handler;

use Throwable;
use Traversable;

trait CompositeHandlerTrait
{
    /**
     * Runs all internal handlers.
     *
     * @throws Throwable If problem running.
     */
    protected function _run()
    {
        $handlers = $this->_getHandlers();
        $this->_beforeAll($handlers);

        foreach ($handlers as $handler) {
            assert($handler instanceof HandlerInterface);
            $handler->run();
            $this->_afterRun($handler);
        }
    }

    /**
     * Invokes after a handler is run.
     *
     * @param HandlerInterface $handler THe handler that was run.
     */
    protected function _afterRun(HandlerInterface $handler)
    {
        // Override this method to do stuff after every handler is run
    }

    /**
     * Invokes before the handlers begin running.
     *
     * @param HandlerInterface $handler THe handler that was run.
     */
    protected function _beforeAll(array $handler)
    {
        // Override this method to do stuff after every handler is run
    }

    /**
     * Retrieves the list of handlers associated with this instance.
     *
     * @return HandlerInterface[]|Traversable A list of handlers.
     */
    abstract protected function _getHandlers();
}