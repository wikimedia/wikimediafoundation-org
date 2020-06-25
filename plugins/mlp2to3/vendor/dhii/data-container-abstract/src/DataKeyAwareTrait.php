<?php

namespace Dhii\Data\Container;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;

/**
 * Functionality for storage and retrieval of a data key.
 *
 * @since [*next-version*]
 */
trait DataKeyAwareTrait
{
    /**
     * The data key, if any.
     *
     * @since [*next-version*]
     *
     * @var string|Stringable|null
     */
    protected $dataKey;

    /**
     * Assigns a data key to this instance.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $key The key.
     *
     * @throws InvalidArgumentException If the key is invalid.
     */
    protected function _setDataKey($key)
    {
        if (!is_null($key) && !is_string($key) && !($key instanceof Stringable)) {
            throw $this->_createInvalidArgumentException($this->__('Data key must be a string or stringable'), 0, null, $key);
        }

        $this->dataKey = $key;

        return $this;
    }

    /**
     * Retrieves the data key associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return string|Stringable|null The key, if any.
     */
    protected function _getDataKey()
    {
        return $this->dataKey;
    }

    /**
     * Creates a new Invalid Argument exception.
     *
     * @since [*next-version*]
     *
     * @param string        $message  The error message.
     * @param int           $code     The error code.
     * @param RootException $previous The inner exception for chaining, if any.
     * @param mixed         $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
            $message = '',
            $code = 0,
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
