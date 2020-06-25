<?php

namespace Dhii\Iterator;

use Dhii\Iterator\Exception\IteratorExceptionInterface;

/**
 * Base functionality for a recursive iterator.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseRecursiveIterator implements RecursiveIteratorInterface
{
    /*
     * Provides basic iterator functionality and recursive iterator functionality, respectively.
     *
     * @since [*next-version*]
     */
    use IteratorTrait, RecursiveIteratorTrait {
        RecursiveIteratorTrait::_valid insteadof IteratorTrait;
    }

    /*
     * Adds recursion mode awareness.
     *
     * @since [*next-version*]
     */
    use ModeAwareTrait;

    /*
     * Provides functionality for determining recursion mode.
     *
     * @since [*next-version*]
     */
    use IsModeCapableTrait;

    /*
     * Adds current temporary iteration instance awareness.
     *
     * @since [*next-version*]
     */
    use IterationAwareTrait;

    /*
     * Provides functionality for creating recursive iteration instances.
     *
     * @since [*next-version*]
     */
    use CreateRecursiveIterationCapableTrait;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws IteratorExceptionInterface If something goes wrong while rewinding.
     */
    public function rewind()
    {
        $this->_rewind();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @throws IteratorExceptionInterface If something goes wrong while advancing.
     */
    public function next()
    {
        $this->_next();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function current()
    {
        return $this->_value();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function key()
    {
        return $this->_key();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function valid()
    {
        return $this->_valid();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getIteration()
    {
        return $this->_getIteration();
    }
}
