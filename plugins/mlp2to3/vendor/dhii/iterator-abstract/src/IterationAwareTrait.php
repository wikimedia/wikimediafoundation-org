<?php

namespace Dhii\Iterator;

/**
 * Functionality for something that is aware of a trait.
 *
 * @since [*next-version*]
 */
trait IterationAwareTrait
{
    /**
     * The iteration instance.
     *
     * @since [*next-version*]
     *
     * @var IterationInterface
     */
    protected $iteration;

    /**
     * Retrieves the iteration instance.
     *
     * @since [*next-version*]
     *
     * @return IterationInterface
     */
    protected function _getIteration()
    {
        return $this->iteration;
    }

    /**
     * Sets the iteration instance.
     *
     * @since [*next-version*]
     *
     * @param IterationInterface|null $iteration The iteration instance.
     */
    protected function _setIteration(IterationInterface $iteration = null)
    {
        $this->iteration = $iteration;
    }
}
