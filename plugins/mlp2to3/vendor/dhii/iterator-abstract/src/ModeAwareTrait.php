<?php

namespace Dhii\Iterator;

/**
 * Functionality for something that is aware of an iteration mode.
 *
 * @since [*next-version*]
 */
trait ModeAwareTrait
{
    /**
     * The iteration mode.
     *
     * @since [*next-version*]
     *
     * @var int
     */
    protected $mode;

    /**
     * Retrieves the iteration mode.
     *
     * @since [*next-version*]
     *
     * @return int
     */
    protected function _getMode()
    {
        return $this->mode;
    }

    /**
     * Sets the iteration mode.
     *
     * @since [*next-version*]
     *
     * @param int $mode The iteration mode.
     *
     * @return $this
     */
    protected function _setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }
}
