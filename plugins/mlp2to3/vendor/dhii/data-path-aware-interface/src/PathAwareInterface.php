<?php

namespace Dhii\Data;

use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Something that can have a path retrieved.
 *
 * @since 0.1
 */
interface PathAwareInterface
{
    /**
     * Retrieve the path that belongs to this instance.
     *
     * @since 0.1
     *
     * @return string|Stringable The path that belongs to this instance.
     */
    public function getPath();
}
