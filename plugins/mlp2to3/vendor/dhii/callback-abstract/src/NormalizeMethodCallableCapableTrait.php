<?php

namespace Dhii\Invocation;

use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use Exception as RootException;
use OutOfRangeException;
use stdClass;
use Traversable;

trait NormalizeMethodCallableCapableTrait
{
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
    protected function _normalizeMethodCallable($callable)
    {
        if (is_object($callable) && is_callable($callable)) {
            return array($callable, '__invoke');
        }

        $amtPartsRequired = 2; // Number of parts required for a valid callable
        $origCallable     = $callable; // Preserving the original value for reporting

        // If stringable, try to separate into parts
        try {
            $callable = $this->_normalizeString($callable);
            $callable = explode('::', $callable, $amtPartsRequired);
        } catch (InvalidArgumentException $e) {
            // Just continue
        }

        // Normalizing to array
        $callable = $this->_normalizeArray($callable);
        $callable = array_slice($callable, 0, $amtPartsRequired);

        // Array must have exactly 2 parts
        if (count($callable) !== $amtPartsRequired) {
            throw $this->_createOutOfRangeException($this->__('The callable must have at least %1$s parts', array($amtPartsRequired)), null, null, $origCallable);
        }

        // First value must be an object or string, because stringable objects should be valid targets
        if (!is_string($callable[0]) && !is_object($callable[0])) {
            throw $this->_createOutOfRangeException($this->__('The first part must be an object or a string'), null, null, $origCallable);
        }

        // The second value must be a string or stringable
        try {
            $callable[1] = $this->_normalizeString($callable[1]);
        } catch (InvalidArgumentException $e) {
            throw $this->_createOutOfRangeException($this->__('The second part must be a string or stringable'), null, $e, $origCallable);
        }

        return $callable;
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
     * Creates a new Out Of Range exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     * @param mixed|null                            $argument The value that is out of range, if any.
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
     * @see   sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = array(), $context = null);
}
