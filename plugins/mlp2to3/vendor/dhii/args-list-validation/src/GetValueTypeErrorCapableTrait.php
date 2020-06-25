<?php

namespace Dhii\Validation;

use InvalidArgumentException;
use ReflectionType;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;

/**
 * Functionality for validating a value against a type.
 *
 * @since [*next-version*]
 */
trait GetValueTypeErrorCapableTrait
{
    /**
     * Generates a list of reasons for a value failing validation against a type spec.
     *
     * @since [*next-version*]
     *
     * @param mixed          $value The value being validated.
     * @param ReflectionType $type  A type criteria.
     *
     * @throws InvalidArgumentException If one of the criteria is invalid.
     *
     * @return string|Stringable|null The error, if value doesn't match the spec; `null` otherwise.
     */
    protected function _getValueTypeError($value, $type)
    {
        if (!($type instanceof ReflectionType)) {
            throw $this->_createInvalidArgumentException($this->__('Type criterion is invalid'), null, null, $type);
        }

        $typeName = method_exists($type, 'getName')
            ? $type->getName()
            : $type->__toString();
        $isOfType = null;

        // If type is built-in, it should be safe to use a built-in function
        if ($type->isBuiltin()) {
            $testFunc = sprintf('is_%1$s', $typeName);
            $isOfType = call_user_func_array($testFunc, [$value]);
        }
        // If type is not built-in, then check whether instance of
        else {
            $isOfType = $value instanceof $typeName;
        }

        if (!$isOfType) {
            return $this->__('Value must be of type "%1$s"', [$typeName]);
        }

        return;
    }

    /**
     * Creates a new Invalid Argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     * @param mixed|null                            $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
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
    abstract protected function __($string, $args = [], $context = null);
}
