<?php

namespace Dhii\Iterator;

/**
 * Something that represents a flattened recursive iteration.
 *
 * @since [*next-version*]
 */
interface RecursiveIterationInterface extends
        DepthAwareIterationInterface,
        PathSegmentsAwareIterationInterface
{
}
