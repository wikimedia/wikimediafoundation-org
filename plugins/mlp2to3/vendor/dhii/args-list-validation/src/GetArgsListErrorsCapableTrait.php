<?php

namespace Dhii\Validation;

use InvalidArgumentException;
use OutOfRangeException;
use ReflectionParameter;
use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;
use ReflectionType;
use stdClass;
use Traversable;

/**
 * Functionality for validating an args list.
 *
 * @since [*next-version*]
 */
trait GetArgsListErrorsCapableTrait
{
    /**
     * Produces a list of reasons why an args list is invalid against a spec.
     *
     * @since [*next-version*]
     *
     * @param array                      $args The list of arguments in order.
     * @param array|Traversable|stdClass $spec The list of criteria.
     *
     * @throws OutOfRangeException If one of the criteria is invalid.
     *
     * @return string[]|Stringable[] The list of errors.
     */
    protected function _getArgsListErrors($args, $spec)
    {
        $errors = [];

        foreach ($spec as $_idx => $_param) {
            if (!($_param instanceof ReflectionParameter)) {
                throw $this->_createOutOfRangeException($this->__('Parameter #%1$d of the specification is invalid', [$_idx]), null, null, $_param);
            }

            $pos          = $_param->getPosition(); // 0-based position index of the arg.
            $isArgPresent = key_exists($pos, $args); // Whether this arg is specified
            $isNullable   = $_param->allowsNull(); // Whether null is allowed

            // Is argument required but not present?
            if (!$_param->isOptional() && !$isArgPresent) {
                $errors[] = $this->__('Argument #%1$s is required', [$pos]);
                continue;
            }

            $arg      = $isArgPresent ? $args[$pos] : null; // The value of the arg
            $isNullOk = $isNullable && $isArgPresent && is_null($arg); // Argument is present, is null, and this is allowed

            if ($isNullOk) {
                continue;
            }

            // Is argument of the right type?
            // Type spec is not for all PHP versions
            if (method_exists($_param, 'hasType') && $_param->hasType()) {
                try {
                    $error = $this->_getValueTypeError($arg, $_param->getType());
                } catch (InvalidArgumentException $e) {
                    throw $this->_createOutOfRangeException($this->__('Problem validating type of argument #%1$d against spec criterion #%1$d', [$pos, $_idx]), null, $e, $_param);
                }

                if (!is_null($error)) {
                    $errors[] = $this->__('Argument #%1$s is invalid: %2$s', [$pos, $this->_normalizeString($error)]);
                }
            }
        }

        return $errors;
    }

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
    abstract protected function _getValueTypeError($value, $type);

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
    abstract protected function __($string, $args = [], $context = null);
}
