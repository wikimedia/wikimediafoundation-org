<?php

namespace Dhii\Collection;

use Iterator;
use Traversable;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use OutOfRangeException;
use Exception as RootException;

/**
 * Awareness of an iterator.
 *
 * @since [*next-version*]
 */
trait IteratorAwareTrait
{
    /**
     * The data store iterator.
     *
     * @since [*next-version*]
     *
     * @var Iterator|null
     */
    protected $iterator;

    /**
     * Retrieves the iterator of this instance's data store.
     *
     * @since [*next-version*]
     *
     * @return Iterator The iterator.
     */
    protected function _getIterator()
    {
        if (is_null($this->iterator)) {
            $this->iterator = $this->_resolveIterator($this->_getDataStore());
        }

        return $this->iterator;
    }

    /**
     * Sets the iterator for this instance.
     *
     * @since [*next-version*]
     *
     * @param Iterator|null $iterator The iterator instance, or null.
     */
    protected function _setIterator($iterator)
    {
        if ($iterator !== null && !($iterator instanceof Iterator)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Invalid iterator'),
                null,
                null,
                $iterator
            );
        }

        $this->iterator = $iterator;
    }

    /**
     * Retrieves a pointer to the data store.
     *
     * @since [*next-version*]
     *
     * @return Traversable The data store.
     */
    abstract protected function _getDataStore();

    /**
     * Finds the deepest iterator that matches.
     *
     * @since [*next-version*]
     *
     * @param Traversable $iterator The iterator to resolve.
     * @param callable    $test     The test function which determines when the iterator is considered to be resolved.
     *                              Default: Returns `true` on first found instance of {@see Iterator}.
     * @param $limit int|float|string|Stringable The depth limit for resolution.
     *
     * @throws InvalidArgumentException If limit is not a valid integer representation.
     * @throws OutOfRangeException      If infinite recursion is detected, or the iterator could not be resolved within the depth limit.
     *
     * @return Iterator The inner-most iterator, or whatever the test function allows.
     */
    abstract protected function _resolveIterator(Traversable $iterator, $test = null, $limit = null);

    /**
     * Creates a new invalid argument exception.
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
