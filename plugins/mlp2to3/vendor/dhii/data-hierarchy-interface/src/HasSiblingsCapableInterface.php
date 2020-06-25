<?php

namespace Dhii\Data\Hierarchy;

/**
 * Something that can check for its siblings.
 *
 * @since 0.1
 */
interface HasSiblingsCapableInterface
{
    /**
     * Checks whether this instance has siblings.
     *
     * @since 0.1
     *
     * @return bool True if this instance has siblings; false otherwise.
     */
    public function hasSiblings();
}
