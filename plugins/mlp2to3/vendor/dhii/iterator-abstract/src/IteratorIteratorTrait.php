<?php

namespace Dhii\Iterator;

use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use Iterator;
use Exception as RootException;

/**
 * Functionality for iterating over another iterator.
 *
 * Intended for use with {@see TrackingIterator}, using an internal iterator for tracking.
 *
 * @since [*next-version*]
 */
trait IteratorIteratorTrait
{
    /**
     * Advances the iterator forward.
     *
     * @since [*next-version*]
     *
     * @param Iterator $tracker The iterator used to track the loop.
     *
     * @throws InvalidArgumentException If problem advancing iterator.
     */
    protected function _advanceTracker($tracker)
    {
        if (!($tracker instanceof Iterator)) {
            throw $this->_createInvalidArgumentException($this->__('Can only advance an Iterator tracker'), null, null, $tracker);
        }

        $tracker->next();
    }

    /**
     * Reset the iterator back to the start.
     *
     * @param Iterator $tracker The iterator used to track the loop.
     *
     * @throws InvalidArgumentException If problem resetting tracker.
     */
    protected function _resetTracker($tracker)
    {
        if (!($tracker instanceof Iterator)) {
            throw $this->_createInvalidArgumentException($this->__('Can only reset an Iterator tracker'), null, null, $tracker);
        }

        $tracker->rewind();
    }

    /**
     * Creates a new iteration using an internal iterator.
     *
     * @since [*next-version*]
     *
     * @param Iterator $tracker The iterator used to track the iteration.
     *
     * @return IterationInterface The new iteration.
     */
    protected function _createIterationFromTracker($tracker)
    {
        if (!($tracker instanceof Iterator)) {
            throw $this->_createInvalidArgumentException($this->__('Can only create an iteration from an Iterator tracker'), null, null, $tracker);
        }

        $key   = $this->_calculateKey($tracker);
        $value = $this->_calculateValue($tracker);

        return $this->_createIteration($key, $value);
    }

    /**
     * Calculates a key based on a given iterator.
     *
     * @since [*next-version*]
     *
     * @param Iterator $iterator The iterator used to calculate the key.
     *
     * @return string|null The calculated key.
     */
    protected function _calculateKey(Iterator $iterator)
    {
        return $iterator->valid()
            ? $iterator->key()
            : null;
    }

    /**
     * Calculates a value based on a given iterator.
     *
     * @since [*next-version*]
     *
     * @param Iterator $iterator The iterator used to calculate the value.
     *
     * @return mixed The calculated value.
     */
    protected function _calculateValue(Iterator $iterator)
    {
        return $iterator->valid()
            ? $iterator->current()
            : null;
    }

    /**
     * Creates a new iteration.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable|null $key   The key for the iteration.
     * @param mixed                                 $value The value for the iteration.
     *
     * @return IterationInterface The new iteration.
     */
    abstract protected function _createIteration($key, $value);

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
     * @see sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
