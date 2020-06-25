<?php

namespace Dhii\Iterator;

use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for creating iterations.
 *
 * @since [*next-version*]
 */
trait CreateIterationCapableTrait
{
    /**
     * Creates a new iteration.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable|null $key   The key for the iteration.
     * @param mixed                                 $value The value for the iteration.
     *
     * @return Iteration The new iteration.
     */
    protected function _createIteration($key, $value)
    {
        return new Iteration($key, $value);
    }
}
