<?php

namespace Dhii\Data\Hierarchy;

/**
 * Something that can check for its children.
 *
 * @since 0.1
 */
interface HasChildrenCapableInterface
{
    /**
     * Checks whether this instance has children.
     *
     * @since 0.1
     *
     * @return bool True if this instance has children; false otherwise.
     */
    public function hasChildren();
}
