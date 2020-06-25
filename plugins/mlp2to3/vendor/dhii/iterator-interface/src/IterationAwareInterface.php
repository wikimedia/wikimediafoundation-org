<?php

namespace Dhii\Iterator;

/**
 * Can retrieve something that represents current temporary state.
 *
 * @since [*next-version*]
 */
interface IterationAwareInterface
{
    /**
     * Retrieves a representation of the current temporary state.
     *
     * @since [*next-version*]
     *
     * @return IterationInterface The state.
     */
    public function getIteration();
}
