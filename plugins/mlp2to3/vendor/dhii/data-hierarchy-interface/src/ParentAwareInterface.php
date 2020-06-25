<?php

namespace Dhii\Data\Hierarchy;

/**
 * Something that can have its parent retrieved.
 *
 * @since 0.1
 */
interface ParentAwareInterface extends HasParentCapableInterface
{
    /**
     * Get this instance's parent.
     *
     * @since 0.1
     *
     * @return mixed The parent of this instance.
     */
    public function getParent();
}
