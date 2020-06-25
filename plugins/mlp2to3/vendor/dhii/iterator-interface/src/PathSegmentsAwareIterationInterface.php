<?php

namespace Dhii\Iterator;

use Dhii\Data\PathSegmentsAwareInterface;

/**
 * An iteration which can have segments of its path retrieved.
 *
 * @since [*next-version*]
 */
interface PathSegmentsAwareIterationInterface extends
        IterationInterface,
        PathSegmentsAwareInterface
{
}
