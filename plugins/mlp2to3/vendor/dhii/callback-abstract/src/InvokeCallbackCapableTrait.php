<?php

namespace Dhii\Invocation;

use InvalidArgumentException;
use stdClass;
use Traversable;
use Exception as RootException;
use Dhii\Invocation\Exception\InvocationExceptionInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use OutOfRangeException;

/**
 * Something that can invoke an internal callback.
 *
 * @since [*next-version*]
 */
trait InvokeCallbackCapableTrait
{
    /**
     * @since [*next-version*]
     *
     * @param Traversable|array|stdClass The list of arguments for the invocation.
     *
     * @throws InvalidArgumentException     If args are not a valid list.
     * @throws OutOfRangeException          If callback is invalid.
     * @throws InvocationExceptionInterface If error during invocation.
     *
     * @return mixed The value resulting from the invocation.
     */
    protected function _invokeCallback($args = array())
    {
        $args     = $this->_normalizeIterable($args);
        $callback = $this->_getCallback();

        try {
            return $this->_invokeCallable($callback, $args);
        } catch (InvalidArgumentException $e) {
            /* We know that `$args` is correct, so the only way
             * `_invokeCallback()` would throw `InvalidArgumentException`
             * is if the callback is wrong. But we cannot let it bubble
             * up, because it is not an argument to this method. Therefore,
             * catch it, wrap, and throw a more appropriate exception.
             */
            throw $this->_createOutOfRangeException(
                $this->__('Invalid callback'),
                null,
                $e,
                $callback
            );
        }
    }

    /**
     * Retrieves the callback.
     *
     * @since [*next-version*]
     *
     * @return callable The callback.
     */
    abstract protected function _getCallback();

    /**
     * Invokes a callable.
     *
     * @since [*next-version*]
     *
     * @param callable                   $callable The callable to invoke.
     * @param array|Traversable|stdClass $args     The arguments to invoke the callable with.
     *
     * @throws InvalidArgumentException     If the callable is not callable.
     * @throws InvalidArgumentException     if the args are not a valid list.
     * @throws InvocationExceptionInterface For errors that happen during invocation.
     *
     * @return mixed The result of the invocation.
     */
    abstract protected function _invokeCallable($callable, $args);

    /**
     * Normalizes an iterable.
     *
     * Makes sure that the return value can be iterated over.
     *
     * @since [*next-version*]
     *
     * @param mixed $iterable The iterable to normalize.
     *
     * @throws InvalidArgumentException If the iterable could not be normalized.
     *
     * @return array|Traversable|stdClass The normalized iterable.
     */
    abstract protected function _normalizeIterable($iterable);

    /**
     * Creates a new  Out Of Range exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The invalid argument, if any.
     *
     * @return OutOfRangeException The new exception.
     */
    abstract protected function _createOutOfRangeException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $argument = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = array(), $context = null);
}
