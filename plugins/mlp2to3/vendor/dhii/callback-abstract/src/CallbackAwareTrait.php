<?php

namespace Dhii\Invocation;

/**
 * Something that has a callback.
 *
 * @since [*next-version*]
 */
trait CallbackAwareTrait
{
    /**
     * The callback.
     *
     * @var callable
     */
    protected $callback;

    /**
     * Retrieves the callback.
     *
     * @since [*next-version*]
     *
     * @return callable
     */
    protected function _getCallback()
    {
        return $this->callback;
    }

    /**
     * Sets the callback.
     *
     * @since [*next-version*
     *
     * @param callable $callback The callback.
     *
     * @return $this
     */
    protected function _setCallback(callable $callback = null)
    {
        $this->callback = $callback;

        return $this;
    }
}
