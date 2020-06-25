<?php

namespace Dhii\Iterator\Exception;

use Dhii\Iterator\IterationAwareInterface;
use Dhii\Iterator\IterationInterface;

/**
 * An exception that occurs in relation to an iteration.
 *
 * @since [*next-version*]
 */
interface IterationExceptionInterface extends
        IteratingExceptionInterface,
        IterationAwareInterface
{
    /**
     * Retrieves the iteration associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return IterationInterface|null The iteration, if any.
     */
    public function getIteration();
}
