<?php

namespace Dhii\Data\Object;

use ArrayAccess;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use OutOfRangeException;
use Psr\Container\ContainerExceptionInterface;
use stdClass;
use Exception as RootException;

/**
 * Functionality for data assignment.
 *
 * @since [*next-version*]
 */
trait SetDataCapableTrait
{
    /**
     * Assign a single piece of data.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key   The key, for which to assign the data.
     *                                                Unless an integer is given, this will be normalized to string.
     * @param mixed                            $value The value to assign.
     *
     * @throws InvalidArgumentException    If key is invalid.
     * @throws OutOfRangeException         If internal data store is invalid.
     * @throws ContainerExceptionInterface If error occurs while writing to container.
     */
    protected function _setData($key, $value)
    {
        $store = $this->_getDataStore();
        try {
            $this->_containerSet($store, $key, $value);
        } catch (InvalidArgumentException $e) {
            throw $this->_createOutOfRangeException($this->__('Invalid store'), null, $e, $store);
        } catch (OutOfRangeException $e) {
            throw $this->_createInvalidArgumentException($this->__('Invalid key'), null, $e, $store);
        }
    }

    /**
     * Retrieves a pointer to the data store.
     *
     * @since [*next-version*]
     *
     * @return array|ArrayAccess|stdClass The data store.
     */
    abstract protected function _getDataStore();

    /**
     * Sets data on the container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass       $container The container to set data on.
     * @param string|int|float|bool|Stringable $key       The key to set the value for.
     * @param mixed                            $value     The value to set.
     *
     * @throws InvalidArgumentException    If the container is invalid.
     * @throws OutOfRangeException         If key is invalid.
     * @throws ContainerExceptionInterface If error occurs while writing to container.
     */
    abstract protected function _containerSet(&$container, $key, $value);

    /**
     * Creates a new Out Of Range exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The value that is out of range, if any.
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
     * Creates a new Dhii invalid argument exception.
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
