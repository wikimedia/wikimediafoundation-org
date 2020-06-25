<?php

namespace Dhii\Iterator;

use Dhii\Data\KeyValueAwareTrait;

/**
 * Base functionality for an iteration.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseIteration implements IterationInterface
{
    /*
     * Provides key and value property management functionality.
     *
     * @since [*next-version*]
     */
    use KeyValueAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getKey()
    {
        return $this->_getKey();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getValue()
    {
        return $this->_getValue();
    }
}
