<?php

namespace Inpsyde\MultilingualPress2to3\Handler;


use cli\progress\Bar;
use Throwable;

trait CompositeProgressTrackingHandlerTrait
{
    protected function _afterRun(HandlerInterface $handler)
    {
        $this->_incrementProgress();
    }

    protected function _resetProgress()
    {
        $total = count($this->_getHandlers());
        $this->_getProgress()->reset($total);
    }

    protected function _incrementProgress()
    {
        $this->_getProgress()->tick();
    }

    abstract protected function _getHandlers();

    /**
     * Retrieves the progress associated with this instance.
     *
     * @return Bar The progress.
     */
    abstract protected function _getProgress();
}