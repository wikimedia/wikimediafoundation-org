<?php

namespace Dhii\Data\Hierarchy;

/**
 * Something that can check for its parent.
 *
 * @since 0.1
 */
interface HasParentCapableInterface
{
    /**
     * Checks whether this instance has a parent.
     *
     * @since 0.1
     *
     * @return bool True if this instance has a parent; false otherwise.
     */
    public function hasParent();
}
