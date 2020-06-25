<?php

namespace Dhii\Iterator;

/**
 * Common functionality for objects that can determine iteration mode.
 *
 * @since [*next-version*]
 */
trait IsModeCapableTrait
{
    /**
     * Determines if the currently selected modes include a specific mode.
     *
     * @since [*next-version*]
     *
     * @param int $mode The mode to check for.
     *
     * @return bool True if mode selected; otherwise, false.
     */
    protected function _isMode($mode)
    {
        return $mode === $this->_getMode();
    }

    /**
     * Retrieves the iteration mode.
     *
     * @since [*next-version*]
     *
     * @return int The mode.
     */
    abstract protected function _getMode();
}
