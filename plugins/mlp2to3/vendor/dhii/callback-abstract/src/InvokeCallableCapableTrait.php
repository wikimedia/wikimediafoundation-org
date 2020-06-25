<?php

namespace Dhii\Invocation;

use Dhii\Exception\InternalExceptionInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Dhii\Invocation\Exception\InvocationExceptionInterface;
use Exception as RootException;
use InvalidArgumentException;
use OutOfRangeException;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use stdClass;
use Traversable;

/**
 * Functionality for invoking a callable.
 *
 * @since [*next-version*]
 */
trait InvokeCallableCapableTrait
{
    /**
     * Invokes a callable.
     *
     * @since [*next-version*]
     *
     * @param callable                   $callable The callable to invoke.
     * @param array|Traversable|stdClass $args     The arguments to invoke the callable with.
     *
     * @throws InvalidArgumentException     If the callable is not callable.
     * @throws InvalidArgumentException     If the args are not a valid list.
     * @throws InvocationExceptionInterface If the callable cannot be invoked.
     * @throws InternalExceptionInterface   If a problem occurs during invocation.
     *
     * @return mixed The result of the invocation.
     */
    protected function _invokeCallable($callable, $args)
    {
        if (!is_callable($callable)) {
            throw $this->_createInvalidArgumentException($this->__('Callable is not callable'), null, null, $callable);
        }

        $args = $this->_normalizeArray($args);

        try {
            $reflection = $this->_createReflectionForCallable($callable);
            $params     = $reflection->getParameters();
            $this->_validateParams($args, $params);
        } catch (RootException $e) {
            throw $this->_createInvocationException($this->__('Could not invoke callable'), null, $e, $callable, $args);
        }

        // Invoke the callable
        try {
            if ($reflection instanceof ReflectionMethod) {
                if (is_object($callable)) {
                    $target = $callable;
                } else {
                    $target = is_object($callable[0])
                        ? $callable[0]
                        : null;
                }

                return $reflection->invokeArgs($target, $args);
            } else {
                return call_user_func_array($callable, $args);
            }
        } catch (RootException $e) {
            throw $this->_createInternalException(
                $this->__('There was an error during invocation'),
                null,
                $e
            );
        }
    }

    /**
     * Creates a reflection for the given callable.
     *
     * @since [*next-version*]
     *
     * @param callable|Stringable|array $callable The callable, or an object that represents a function FQN, or a
     *                                            callable-like array where the method is stringable.
     *
     * @throws InvalidArgumentException If the callable type is invalid.
     * @throws OutOfRangeException      If the callable format is wrong.
     * @throws ReflectionException      If a reflection could not be created.
     *
     * @return ReflectionFunction|ReflectionMethod The reflection.
     */
    abstract protected function _createReflectionForCallable($callable);

    /**
     * Validates a function or method's arguments according to the method's parameter specification.
     *
     * @since [*next-version*]
     *
     * @param array                                      $args The arguments to validate.
     * @param ReflectionParameter[]|stdClass|Traversable $spec The parameter specification.
     */
    abstract protected function _validateParams($args, $spec);

    /**
     * Normalizes a value into an array.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|Traversable $value The value to normalize.
     *
     * @throws InvalidArgumentException If value cannot be normalized.
     *
     * @return array The normalized value.
     */
    abstract protected function _normalizeArray($value);

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see   sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = array(), $context = null);

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
    abstract protected function _createInvocationException(
        $message = null,
        $code = null,
        RootException $previous = null,
        callable $callable = null,
        $args = null
    );

    /**
     * Creates a new Internal exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException          $previous The internal cause for this problem.
     *
     * @return InternalExceptionInterface The new exception.
     */
    abstract protected function _createInternalException(
        $message = null,
        $code = null,
        RootException $previous = null
    );

    /**
     * Creates a new Invalid Argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $argument = null
    );
}
