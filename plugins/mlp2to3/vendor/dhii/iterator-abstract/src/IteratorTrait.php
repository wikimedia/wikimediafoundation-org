<?php

namespace Dhii\Iterator;

use Dhii\Iterator\Exception\IteratorExceptionInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;

/**
 * Common functionality for objects that can iterate.
 *
 * @since [*next-version*]
 */
trait IteratorTrait
{
    /**
     * Resets the iterator.
     *
     * @since [*next-version*]
     * @see Iterator::rewind()
     */
    protected function _rewind()
    {
        try {
            $this->_setIteration($this->_reset());
        } catch (RootException $exception) {
            $this->_throwIteratorException(
                $this->__('An error occurred while rewinding'),
                null,
                $exception
            );
        }
    }

    /**
     * Advances the iterator to the next element.
     *
     * @since [*next-version*]
     * @see Iterator::next()
     *
     * @throws IteratorExceptionInterface If an error occurred during iteration.
     */
    protected function _next()
    {
        try {
            $this->_setIteration($this->_loop());
        } catch (RootException $exception) {
            $this->_throwIteratorException(
                $this->__('An error occurred while iterating'),
                null,
                $exception
            );
        }
    }

    /**
     * Retrieves the key of the current iteration.
     *
     * @since [*next-version*]
     * @see Iterator::key()
     *
     * @return string|null The key, if iterating; otherwise, null.
     */
    protected function _key()
    {
        return $this->_getIteration()->getKey();
    }

    /**
     * Retrieves the value of the current iteration.
     *
     * @since [*next-version*]
     * @see Iterator::current()
     *
     * @return mixed The value.
     */
    protected function _value()
    {
        return $this->_getIteration()->getValue();
    }

    /**
     * Determines whether the current state of the iterator is valid.
     *
     * @since [*next-version*]
     * @see Iterator::valid()
     *
     * @return bool True if current state is valid; false otherwise;
     */
    protected function _valid()
    {
        return $this->_key() !== null;
    }

    /**
     * Computes a reset state.
     *
     * @since [*next-version*]
     *
     * @return IterationInterface The iteration that represents the new state.
     */
    abstract protected function _reset();

    /**
     * Advances the iterator and computes the new state.
     *
     * @since [*next-version*]
     *
     * @return IterationInterface The iteration that represents the new state.
     */
    abstract protected function _loop();

    /**
     * Retrieves the current iteration.
     *
     * @since [*next-version*]
     *
     * @return IterationInterface|null The current iteration, if any.
     */
    abstract protected function _getIteration();

    /**
     * Assigns an iteration to this instance.
     *
     * @since [*next-version*]
     *
     * @param IterationInterface|null $iteration The iteration to set.
     */
    abstract protected function _setIteration(IterationInterface $iteration = null);

    /**
     * Throws a new iterator exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     *
     * @return IteratorExceptionInterface The created exception.
     */
    abstract protected function _throwIteratorException(
        $message = null,
        $code = null,
        RootException $previous = null
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
