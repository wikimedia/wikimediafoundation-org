<?php

namespace Dhii\Collection;

use Dhii\Data\Container\AbstractBaseContainer;
use Dhii\Data\Container\ContainerGetCapableTrait;
use Dhii\Data\Container\ContainerHasCapableTrait;
use Dhii\Data\Container\ContainerInterface;
use Dhii\Data\Container\NormalizeContainerCapableTrait;
use Dhii\Data\Object\DataStoreAwareContainerTrait;
use Dhii\Data\Object\GetDataCapableTrait;
use Dhii\Data\Object\HasDataCapableTrait;
use Dhii\Data\Object\NormalizeKeyCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\Iterator\CreateIterationCapableTrait;
use Dhii\Iterator\CreateIteratorExceptionCapableTrait;
use Dhii\Iterator\IterationAwareTrait;
use Dhii\Iterator\IteratorInterface;
use Dhii\Iterator\IteratorIteratorTrait;
use Dhii\Iterator\IteratorTrait;
use Dhii\Iterator\ResolveIteratorCapableTrait;
use Dhii\Iterator\TrackingIteratorTrait;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Exception as RootException;

/**
 * Common functionality for maps.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseMap extends AbstractBaseContainer implements
    /*
     * @see https://bugs.php.net/bug.php?id=60161
     * @since [*next-version*]
     */
    IteratorInterface,
    /* @since [*next-version*] */
    MapInterface,
    /* @since [*next-version*] */
    ContainerInterface
{
    /* Basic Dhii iterator functionality.
     *
     * @since [*next-version*]
     */
    use IteratorTrait;

    /* Functionality for iterators that use a tracker.
     *
     * @since [*next-version*]
     */
    use TrackingIteratorTrait;

    /* Functionality for tracking iterators that use an inner iterator.
     *
     * @since [*next-version*]
     */
    use IteratorIteratorTrait;

    /* Awareness of an iteration.
     *
     * @since [*next-version*]
     */
    use IterationAwareTrait;

    /* Awareness of an iterator.
     *
     * @since [*next-version*]
     */
    use IteratorAwareTrait;

    /* Ability to resolve an iterator.
     *
     * @since [*next-version*]
     */
    use ResolveIteratorCapableTrait;

    /* Ability retrieve data from an internal container.
     *
     * @since [*next-version*]
     */
    use GetDataCapableTrait;

    /* Ability to check for data in an internal container.
     *
     * @since [*next-version*]
     */
    use HasDataCapableTrait;

    /* Ability to retrieve data for a key from a container.
     *
     * @since [*next-version*]
     */
    use ContainerGetCapableTrait;

    /* Ability to check for data key on a container.
     *
     * @since [*next-version*]
     */
    use ContainerHasCapableTrait;

    /* Data store awareness
     *
     * @since [*next-version*]
     */
    use DataStoreAwareContainerTrait;

    /* Factory of iterations.
     *
     * @since [*next-version*]
     */
    use CreateIterationCapableTrait;

    /* Factory of Iterator exceptions.
     *
     * @since [*next-version*]
     */
    use CreateIteratorExceptionCapableTrait;

    /* Factory of Out of Range exceptions.
     *
     * @since [*next-version*]
     */
    use CreateOutOfRangeExceptionCapableTrait;

    /* Factory of Invalid Argument exceptions.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* Ability to normalize containers.
     *
     * @since [*next-version*]
     */
    use NormalizeContainerCapableTrait;

    /* Ability to normalize integers.
     *
     * @since [*next-version*]
     */
    use NormalizeIntCapableTrait;

    /* Ability to normalize strings.
     *
     * @since [*next-version*]
     */
    use NormalizeStringCapableTrait;

    /* Ability to normalize container keys.
     *
     * @since [*next-version*]
     */
    use NormalizeKeyCapableTrait;

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
    public function current()
    {
        return $this->_value();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
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
    public function valid()
    {
        return $this->_valid();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function rewind()
    {
        $this->_rewind();
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

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getTracker()
    {
        return $this->_getIterator();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _throwIteratorException(
        $message = null,
        $code = null,
        RootException $previous = null
    ) {
        throw $this->_createIteratorException($message, $code, $previous, $this);
    }
}
