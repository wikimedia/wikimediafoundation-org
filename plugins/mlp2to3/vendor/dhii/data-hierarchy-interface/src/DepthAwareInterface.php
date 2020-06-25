<?php

namespace Dhii\Data\Hierarchy;

/**
 * Something that can have its depth retrieved.
 * 
 * Here, depth represents the level of nesting.
 *
 * @since [*next-version*]
 */
interface DepthAwareInterface
{
    /**
     * Retrieve the depth of this instance.
     *
     * The depth of a node is the number of edges from the node to the tree's
     * root node.
     *
     * @since [*next-version*]
     *
     * @return int The depth.
     */
    public function getDepth();
}
