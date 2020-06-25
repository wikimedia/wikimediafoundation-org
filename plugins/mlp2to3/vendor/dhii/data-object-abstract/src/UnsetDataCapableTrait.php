<?php

namespace Dhii\Data\Object;

use ArrayAccess;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;
use OutOfRangeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

/**
 * Functionality for data retrieval.
 *
 * @since [*next-version*]
 */
trait UnsetDataCapableTrait
{
    /**
     * Unset data by key.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key The key of data to unset.
     *
     * @throws InvalidArgumentException    If the key is invalid.
     * @throws OutOfRangeException         If the inner store is invalid.
     * @throws NotFoundExceptionInterface  If the key is not found.
     * @throws ContainerExceptionInterface If problem accessing the container.
     */
    protected function _unsetData($key)
    {
        $store = $this->_getDataStore();

        try {
            $this->_containerUnset($store, $key);
        } catch (InvalidArgumentException $e) {
            throw $this->_createOutOfRangeException($this->__('Invalid store'),  null, $e, $store);
        } catch (OutOfRangeException $e) {
            throw $this->_createInvalidArgumentException($this->__('Invalid key'), null, $e, $key);
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
     * Unsets a value with the specified key on the given container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass       $container The writable container to unset the value on.
     * @param string|int|float|bool|Stringable $key       The key to unset the value for.
     *
     * @throws InvalidArgumentException    If the container is invalid.
     * @throws OutOfRangeException         If the key is invalid.
     * @throws NotFoundExceptionInterface  If the key is not found.
     * @throws ContainerExceptionInterface If problem accessing the container.
     */
    abstract protected function _containerUnset(&$container, $key);

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
     * Creates a new Invalid Argument exception.
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
