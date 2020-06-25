<?php

namespace Dhii\Cache;

/**
 * A simple cache that stores values in memory.
 *
 * @since [*next-version*]
 */
class MemoryMemoizer extends AbstractBaseSimpleCacheMemory
{
    /**
     * @since [*next-version*]
     */
    public function __construct()
    {
        $this->_construct();
    }

    /**
     * {@inheritdoc}
     *
     * Clears the internal data.
     *
     * @since [*next-version*]
     */
    protected function _construct()
    {
        parent::_construct();
        $this->clear();
    }
}
