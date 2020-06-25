<?php

namespace Dhii\Invocation;

use Dhii\Validation\Exception\ValidationFailedExceptionInterface;
use Dhii\Validation\ValidatorInterface;
use OutOfRangeException;
use ReflectionParameter;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use stdClass;
use Traversable;

/**
 * Functionality for validating parameters according to type.
 *
 * @since [*next-version*]
 */
trait ValidateParamsCapableTrait
{
    /**
     * Validates a function or method's arguments according to the method's parameter specification.
     *
     * @since [*next-version*]
     *
     * @param array                                      $args The arguments to validate.
     * @param ReflectionParameter[]|stdClass|Traversable $spec The parameter specification.
     */
    protected function _validateParams($args, $spec)
    {
        $errors = $this->_getArgsListErrors($args, $spec);
        if ($this->_countIterable($errors)) {
            throw $this->_createValidationFailedException(null, null, null, null, $args, $errors);
        }
    }

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
    abstract protected function _getArgsListErrors($args, $spec);

    /**
     * Counts the elements in an iterable.
     *
     * Is optimized to retrieve count from values that support it.
     * - If {@see stdClass} instance, will enumerate the properties into an array.
     * - If array, will count in regular way using count();
     * - If {@see Countable}, will do the same;
     * - If {@see IteratorAggregate}, will drill down into internal iterators
     * until the first {@see Countable} is encountered, in which case the same
     * as above will be done.
     * - In any other case, will apply {@see iterator_count()}, which means
     * that it will iterate over the whole traversable to determine the count.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|Traversable $iterable The iterable to count. Must be finite.
     *
     * @return int The amount of elements.
     */
    abstract protected function _countIterable($iterable);

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
     * Creates a new Validation exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null                 $message          The message, if any
     * @param int|null                               $code             The error code, if any.
     * @param RootException|null                     $previous         The inner exception, if any.
     * @param ValidatorInterface|null                $validator        The validator which triggered the exception, if any.
     * @param mixed|null                             $subject          The subject that has failed validation, if any.
     * @param string[]|Stringable[]|Traversable|null $validationErrors The errors that are to be associated with the new exception, if any.
     *
     * @return ValidationFailedExceptionInterface The new exception.
     */
    abstract protected function _createValidationFailedException(
        $message = null,
        $code = null,
        RootException $previous = null,
        ValidatorInterface $validator = null,
        $subject = null,
        $validationErrors = null
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
