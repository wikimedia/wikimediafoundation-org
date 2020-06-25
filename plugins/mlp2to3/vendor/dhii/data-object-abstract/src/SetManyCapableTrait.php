<?php

namespace Dhii\Data\Object;

use ArrayAccess;
use OutOfRangeException;
use Psr\Container\ContainerExceptionInterface;
use stdClass;
use Traversable;
use Exception as RootException;
use InvalidArgumentException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for data assignment.
 *
 * @since [*next-version*]
 */
trait SetManyCapableTrait
{
    /**
     * Assign data.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|Traversable $data The data to set. Existing keys will be overwritten.
     *                                         The rest of the data remains unaltered.
     *
     * @throws InvalidArgumentException    If the data map is invalid.
     * @throws OutOfRangeException         If the internal data store one of the data keys to set is invalid.
     * @throws ContainerExceptionInterface If a problem with setting data occurs.
     */
    protected function _setMany($data)
    {
        $data  = $this->_normalizeIterable($data);
        $store = $this->_getDataStore();

        try {
            $this->_containerSetMany($store, $data);
        } catch (InvalidArgumentException $e) {
            throw $this->_createOutOfRangeException($this->__('Invalid store'), null, $e, $store);
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
     * Sets multiple values on the container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass $container The container to set data on.
     * @param array|Traversable|stdClass $data      The map of data to set on the container.
     *
     * @throws InvalidArgumentException    If the container or the data map is invalid.
     * @throws OutOfRangeException         If one of the data keys is invalid.
     * @throws ContainerExceptionInterface If a problem with setting data occurs.
     */
    abstract protected function _containerSetMany(&$container, $data);

    /**
     * Normalizes an iterable.
     *
     * Makes sure that the return value can be iterated over.
     *
     * @since [*next-version*]
     *
     * @param mixed $iterable The iterable to normalize.
     *
     * @throws InvalidArgumentException If the iterable could not be normalized.
     *
     * @return array|Traversable|stdClass The normalized iterable.
     */
    abstract protected function _normalizeIterable($iterable);

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
