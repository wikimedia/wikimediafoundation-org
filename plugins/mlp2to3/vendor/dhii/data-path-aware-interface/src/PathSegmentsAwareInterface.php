<?php

namespace Dhii\Data;

use Traversable;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Something that can have path segments retrieved from it.
 *
 * @since 0.1
 */
interface PathSegmentsAwareInterface
{
    /**
     * Retrieves a list of path segments.
     *
     * @since 0.1
     *
     * @return string[]|Stringable[]|Traversable The list of segments.
     */
    public function getPathSegments();
}
