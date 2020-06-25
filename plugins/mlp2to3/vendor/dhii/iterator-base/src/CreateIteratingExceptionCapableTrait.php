<?php

namespace Dhii\Iterator;

use Dhii\Iterator\Exception\IteratingException;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;

/**
 * Common functionality for objects that can create iterating exceptions.
 *
 * @since [*next-version*]
 */
trait CreateIteratingExceptionCapableTrait
{
    /**
     * Creates a new iterating exception instance.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The previous exception for chaining, if any.
     *
     * @return IteratingException The created exception instance.
     */
    protected function _createIteratingException($message = null, $code = null, RootException $previous = null)
    {
        return new IteratingException($message, $code, $previous);
    }
}
