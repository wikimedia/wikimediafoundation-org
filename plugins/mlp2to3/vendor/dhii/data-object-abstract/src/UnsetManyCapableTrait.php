<?php

namespace Dhii\Data\Object;

use ArrayAccess;
use OutOfRangeException;
use Exception as RootException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;
use Traversable;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;

/**
 * Functionality for unsetting multiple keys at once.
 *
 * @since [*next-version*]
 */
trait UnsetManyCapableTrait
{
    /**
     * Unset data by keys.
     *
     * @since [*next-version*]
     *
     * @param string[]|int[]|float[]|bool[]|Stringable[]|stdClass|Traversable $keys The keys of data to unset.
     *
     * @throws InvalidArgumentException    If the list of keys is invalid.
     * @throws OutOfRangeException         If the internal store or one of the keys is invalid.
     * @throws NotFoundExceptionInterface  If one of the keys is not found.
     * @throws ContainerExceptionInterface If problem accessing the container.
     */
    protected function _unsetMany($keys)
    {
        $keys  = $this->_normalizeIterable($keys);
        $store = $this->_getDataStore();

        try {
            $this->_containerUnsetMany($store, $keys);
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
     * Unsets values with the specified keys on the given container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass                                      $container The writable container to unset the values on.
     * @param string[]|Stringable[]|bool[]|int[]|float[]|Traversable|stdClass $keys      The list keys to unset the values for.
     *
     * @throws InvalidArgumentException    If the container or the list of keys is invalid.
     * @throws OutOfRangeException         If one of the keys is invalid.
     * @throws NotFoundExceptionInterface  If one of the keys is not found.
     * @throws ContainerExceptionInterface If problem accessing the container.
     */
    abstract protected function _containerUnsetMany(&$container, $keys);

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
