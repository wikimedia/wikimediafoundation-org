<?php

namespace Dhii\Iterator;

use Exception as RootException;

/**
 * Functionality for iterators that use a tracker to keep track of the current position.
 *
 * @since [*next-version*]
 */
trait TrackingIteratorTrait
{
    /**
     * Resets the loop and calculates the first iteration.
     *
     * @since [*next-version*]
     *
     * @throws RootException If problem resetting.
     *
     * @return IterationInterface The iteration that represents the new state.
     */
    protected function _reset()
    {
        $tracker = $this->_getTracker();
        $this->_resetTracker($tracker);
        $iteration = $this->_createIterationFromTracker($tracker);

        return $iteration;
    }

    /**
     * Advances the loop and calculates the next iteration.
     *
     * @since [*next-version*]
     *
     * @throws RootException If problem looping.
     *
     * @return IterationInterface The iteration that represents the new state.
     */
    protected function _loop()
    {
        $tracker = $this->_getTracker();
        $this->_advanceTracker($tracker);
        $iteration = $this->_createIterationFromTracker($tracker);

        return $iteration;
    }

    /**
     * Retrieves the tracker used to track the loop.
     *
     * @since [*next-version*]
     *
     * @return mixed The tracker.
     */
    abstract protected function _getTracker();

    /**
     * Advances the tracker forward.
     *
     * @since [*next-version*]
     *
     * @param mixed $tracker The tracker used to track the loop.
     *
     * @throws RootException If problem advancing tracker.
     */
    abstract protected function _advanceTracker($tracker);

    /**
     * Reset the tracker back to the start.
     *
     * @param mixed $tracker The tracker used to track the loop.
     *
     * @throws RootException If problem resetting tracker.
     */
    abstract protected function _resetTracker($tracker);

    /**
     * Creates a new iteration using a tracker.
     *
     * @since [*next-version*]
     *
     * @param mixed $tracker The tracker used to track the iteration.
     *
     * @return IterationInterface The new iteration.
     */
    abstract protected function _createIterationFromTracker($tracker);
}
