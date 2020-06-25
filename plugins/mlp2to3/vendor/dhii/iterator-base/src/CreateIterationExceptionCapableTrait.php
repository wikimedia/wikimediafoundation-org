<?php

namespace Dhii\Iterator;

use Dhii\Iterator\Exception\IterationException;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;

/**
 * Common functionality for objects that can create iteration exceptions.
 *
 * @since [*next-version*]
 */
trait CreateIterationExceptionCapableTrait
{
    /**
     * Creates a new iteration exception instance.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null  $message   The error message, if any.
     * @param int|null                $code      The error code, if any.
     * @param RootException|null      $previous  The previous exception for chaining, if any.
     * @param IterationInterface|null $iteration The iteration instance, if any.
     *
     * @return IterationException The created exception instance.
     */
    protected function _createIterationException(
        $message = null,
        $code = null,
        RootException $previous = null,
        IterationInterface $iteration = null
    ) {
        return new IterationException($message, $code, $previous, $iteration);
    }
}
