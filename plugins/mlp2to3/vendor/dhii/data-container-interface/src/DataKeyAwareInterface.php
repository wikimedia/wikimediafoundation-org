<?php

namespace Dhii\Data\Container;

use Util\String\StringableInterface as Stringable;

/**
 * Something that can have a data key retrieved from it.
 *
 * @since 0.2
 */
interface DataKeyAwareInterface
{
    /**
     * Retrieves the key that is associated with this instance.
     *
     * @since 0.2
     *
     * @return string|Stringable|null The key, if any.
     */
    public function getDataKey();
}
