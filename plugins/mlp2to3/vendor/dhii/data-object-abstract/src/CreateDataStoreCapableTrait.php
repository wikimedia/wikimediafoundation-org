<?php

namespace Dhii\Data\Object;

use ArrayObject;
use InvalidArgumentException;
use stdClass;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;

/**
 * Functionality for data store creation.
 *
 * @since [*next-version*]
 */
trait CreateDataStoreCapableTrait
{
    /**
     * Creates a new data store.
     *
     * @since [*next-version*]
     *
     * @param stdClass|array|null $data The data for the store, if any.
     *
     * @throws InvalidArgumentException If the type of data for the store is invalid.
     *
     * @return ArrayObject The new data store.
     */
    protected function _createDataStore($data = null)
    {
        // Default
        if (is_null($data)) {
            $data = [];
        }

        try {
            // Constructor already throws in PHP 5+, but doesn't supply the value.
            return new ArrayObject($data);
        } catch (InvalidArgumentException $e) {
            throw $this->_createInvalidArgumentException(
                $this->__('Invalid type of store data'),
                null,
                $e,
                $data
            );
        }
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
