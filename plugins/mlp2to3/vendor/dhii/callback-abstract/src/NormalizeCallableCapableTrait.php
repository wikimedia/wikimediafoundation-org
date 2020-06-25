<?php

namespace Dhii\Invocation;

use Closure;
use InvalidArgumentException;
use OutOfRangeException;
use Dhii\Util\String\StringableInterface as Stringable;
use stdClass;
use Traversable;

/**
 * Functionality for callable normalization.
 *
 * @since [*next-version*]
 */
trait NormalizeCallableCapableTrait
{
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
    public function _normalizeCallable($callable)
    {
        // Closure remains as such
        if ($callable instanceof Closure) {
            return $callable;
        }

        // Invocable objects take precedence over stringable ones
        if (!(is_object($callable) && is_callable($callable))) {
            try {
                // Strings that don't identify methods get normalized and returned
                $callable = $this->_normalizeString($callable);
                if (strpos($callable, '::', 1) === false) {
                    return $callable;
                }
            } catch (InvalidArgumentException $e) {
                // Continue
            }
        }

        // Everything else is a method
        return $this->_normalizeMethodCallable($callable);
    }

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
     * Normalizes a method representation into a format recognizable by PHP as callable.
     *
     * This does not guarantee that the callable will actually be invocable at this time; only that it looks like
     * something that can be invoked. See the second argument of {@see is_callable()).
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|stdClass|Traversable|array $callable The stringable that identifies a static method,
     *                                                               or a list, where the first element is a class or class name, and the second is the name of the method. If the list has
     *                                                               more than 2 parts, other parts will be removed. The second element may be a Stringable object, and will then
     *                                                               be normalized. The first element MUST be a string or object, and will not be normalized.
     *
     * @see is_callable()
     *
     * @throws InvalidArgumentException If the argument is not stringable and not a list.
     * @throws OutOfRangeException      If the argument does not contain enough parts to make a callable, if one of the parts is invalid.
     *
     * @return array An array, where the first element is an object or a class name, and the second element is the name of a method.
     */
    abstract protected function _normalizeMethodCallable($callable);
}
