<?php

namespace Dhii\Data\Object;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use stdClass;
use ArrayAccess;

/**
 * Functionality for retrieval of the data store.
 *
 * @since [*next-version*]
 */
trait DataStoreAwareContainerTrait
{
    /**
     * The data store.
     *
     * @since [*next-version*]
     *
     * @var array|ArrayAccess|ContainerInterface|stdClass|null
     */
    protected $dataStore;

    /**
     * Retrieves a pointer to the data store.
     *
     * @since [*next-version*]
     *
     * @return array|ArrayAccess|stdClass|ContainerInterface|null The data store.
     */
    protected function _getDataStore()
    {
        return $this->dataStore;
    }

    /**
     * Assigns a data store to this instance.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|ContainerInterface|null $dataStore A container.
     *
     * @throws InvalidArgumentException If internal data store is invalid.
     */
    protected function _setDataStore($dataStore)
    {
        if (!is_null($dataStore)) {
            $dataStore = $this->_normalizeContainer($dataStore);
        }

        $this->dataStore = $dataStore;
    }

    /**
     * Normalizes a container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|ContainerInterface $container The container to normalize.
     *
     * @throws InvalidArgumentException If the container is invalid.
     *
     * @return array|ArrayAccess|stdClass|ContainerInterface A readable container.
     */
    abstract protected function _normalizeContainer($container);
}
