<?php

namespace Dhii\Data\Object;

use ArrayAccess;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use stdClass;

/**
 * Functionality for data checking.
 *
 * @since [*next-version*]
 */
trait HasDataCapableTrait
{
    /**
     * Check data by key.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key The key, for which to check the data.
     *                                              Unless an integer is given, this will be normalized to string.
     *
     * @throws InvalidArgumentException    If key is invalid.
     * @throws ContainerExceptionInterface If an error occurred while reading from the container.
     *
     * @return bool True if data for the specified key exists; false otherwise.
     */
    protected function _hasData($key)
    {
        $store = $this->_getDataStore();

        return $this->_containerHas($store, $key);
    }

    /**
     * Retrieves a pointer to the data store.
     *
     * @since [*next-version*]
     *
     * @return array|ArrayAccess|stdClass|ContainerInterface The data store.
     */
    abstract protected function _getDataStore();

    /**
     * Retrieves an entry from a container or data set.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|ContainerInterface $container The container to read from.
     * @param string|int|float|bool|Stringable              $key       The key of the value to retrieve.
     *
     * @throws ContainerExceptionInterface If an error occurred while reading from the container.
     *
     * @return bool True if the container has an entry for the given key, false if not.
     */
    abstract protected function _containerHas($container, $key);
}
