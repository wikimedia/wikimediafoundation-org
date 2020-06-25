<?php

namespace Dhii\Invocation;

use Closure;
use InvalidArgumentException;
use OutOfRangeException;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for creating a reflection for a callable.
 *
 * @since [*next-version*]
 */
trait CreateReflectionForCallableCapableTrait
{
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
    protected function _createReflectionForCallable($callable)
    {
        $callable = $this->_normalizeCallable($callable);

        // String or closure means function
        if (is_string($callable) || ($callable instanceof Closure)) {
            return $this->_createReflectionFunction($callable);
        }

        // Otherwise array, which means method
        $object = $callable[0];
        $class  = is_string($object) ? $object : get_class($object);
        $method = $callable[1];

        return $this->_createReflectionMethod($class, $method);
    }

    /**
     * Normalizes a callable, such that it is possible to distinguish between function and method formats.
     *
     * - 'MyClass:myMethod' -> ['MyClass', 'myMethod'].
     * - Closures remain closures.
     * - Invocable object -> [$object, '__invoke']
     * - An object that is both stringable and invocable will be treated as invocable.
     * - Arrays remain arrays, with normalization.
     *
     * @param array|Stringable|string|object|Closure.
     *
     * @since [*next-version*]
     *
     * @throws InvalidArgumentException If the type of callable is wrong.
     * @throws OutOfRangeException      If the format of the callable is wrong.
     *
     * @return array|Closure|string The normalized callable.
     */
    abstract public function _normalizeCallable($callable);

    /**
     * Normalizes a value to its string representation.
     *
     * The values that can be normalized are any scalar values, as well as
     * {@see StringableInterface).
     *
     * @since [*next-version*]
     *
     * @param Stringable|string|int|float|bool $subject The value to normalize to string.
     *
     * @throws InvalidArgumentException If the value cannot be normalized.
     *
     * @return string The string that resulted from normalization.
     */
    abstract protected function _normalizeString($subject);

    /**
     * Creates a new reflection for a method of a class.
     *
     * The function must exist.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable $className  The class name.
     * @param string|Stringable $methodName The method name.
     *
     * @throws ReflectionException If the reflection could not be created.
     *
     * @return ReflectionMethod The new reflection.
     */
    abstract protected function _createReflectionMethod($className, $methodName);

    /**
     * Creates a new reflection for a method.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable        $methodName   The method name.
     * @param object|string|Stringable $functionName The name of the function.
     *
     * @throws ReflectionException If the reflection could not be created.
     *
     * @return ReflectionFunction The new reflection.
     */
    abstract protected function _createReflectionFunction($functionName);
}
