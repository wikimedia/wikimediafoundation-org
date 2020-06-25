<?php

namespace Dhii\Data\Hierarchy;

use Traversable;

/**
 * Something that can have its children retrieved.
 *
 * @since 0.1
 */
interface ChildrenAwareInterface extends HasChildrenCapableInterface
{
    /**
     * Get a list of this instance's children.
     *
     * @since 0.1
     *
     * @return mixed[]|Traversable A list of children that belong to this instance.
     */
    public function getChildren();
}
