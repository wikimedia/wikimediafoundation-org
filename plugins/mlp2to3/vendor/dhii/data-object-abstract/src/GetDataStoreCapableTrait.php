<?php

namespace Dhii\Data\Object;

use ArrayObject;
use InvalidArgumentException;
use stdClass;

/**
 * Functionality for retrieval of the data store.
 *
 * @since [*next-version*]
 */
trait GetDataStoreCapableTrait
{
    /**
     * The data store.
     *
     * @since [*next-version*]
     *
     * @var ArrayObject|null
     */
    protected $dataStore;

    /**
     * Retrieves a pointer to the data store.
     *
     * @since [*next-version*]
     *
     * @return ArrayObject The data store.
     */
    protected function _getDataStore()
    {
        return $this->dataStore === null
                ? $this->dataStore = $this->_createDataStore()
                : $this->dataStore;
    }

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
    abstract protected function _createDataStore($data = null);
}
