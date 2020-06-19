<?php

namespace Inpsyde\MultilingualPress2to3\Handler;

use Throwable;
use Traversable;

class CompositeHandler implements HandlerInterface
{
    use CompositeHandlerTrait;

    protected $handlers;

    /**
     * Handler constructor.
     *
     * @param HandlerInterface[]|Traversable $handlers The configuration of this handler.
     */
    public function __construct($handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getHandlers()
    {
        return $this->handlers;
    }

    /**
     * {@inheritdoc}
     *
     * @throws Throwable
     */
    public function run()
    {
        $this->_run();
    }
}