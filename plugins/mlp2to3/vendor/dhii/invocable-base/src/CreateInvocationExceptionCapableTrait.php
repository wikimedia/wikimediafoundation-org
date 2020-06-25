<?php

namespace Dhii\Invocation;

use Dhii\Invocation\Exception\InvocationException;
use Dhii\Invocation\Exception\InvocationExceptionInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use Traversable;

/**
 * Functionality for creating invocation exception instances.
 *
 * @since [*next-version*]
 */
trait CreateInvocationExceptionCapableTrait
{
    /**
     * Creates a new Invocation exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param callable               $callable The callable that caused the problem, if any.
     * @param Traversable|array      $args     The associated list of arguments, if any.
     *
     * @return InvocationExceptionInterface The new exception.
     */
    protected function _createInvocationException(
        $message = null,
        $code = null,
        RootException $previous = null,
        callable $callable = null,
        $args = null
    ) {
        return new InvocationException($message, $code, $previous, $callable, $args);
    }
}
