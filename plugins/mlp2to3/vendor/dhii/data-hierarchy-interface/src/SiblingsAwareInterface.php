<?php

namespace Dhii\Data\Hierarchy;

use Traversable;

/**
 * Something that can have its siblings retrieved.
 *
 * @since 0.1
 */
interface SiblingsAwareInterface extends HasSiblingsCapableInterface
{
    /**
     * Get a list of this instance's siblings.
     *
     * @since 0.1
     *
     * @return mixed[]|Traversable A list of siblings of this instance.
     */
    public function getSiblings();
}
