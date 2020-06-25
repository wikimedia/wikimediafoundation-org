<?php

namespace Dhii\Iterator;

use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;

/**
 * Common functionality for objects that are aware of an iterator.
 *
 * @since [*next-version*]
 */
trait IteratorAwareTrait
{
    /**
     * The iterator instance, if any.
     *
     * @since [*next-version*]
     *
     * @var IteratorInterface|null
     */
    protected $iterator;

    /**
     * Retrieves the iterator associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return IteratorInterface|null The iterator instance, if any.
     */
    protected function _getIterator()
    {
        return $this->iterator;
    }

    /**
     * Sets the iterator for this instance.
     *
     * @since [*next-version*]
     *
     * @param IteratorInterface|null $iterator The iterator instance, or null.
     */
    protected function _setIterator($iterator)
    {
        if ($iterator !== null && !($iterator instanceof IteratorInterface)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Argument is not an iterator instance'),
                null,
                null,
                $iterator
            );
        }

        $this->iterator = $iterator;
    }

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
