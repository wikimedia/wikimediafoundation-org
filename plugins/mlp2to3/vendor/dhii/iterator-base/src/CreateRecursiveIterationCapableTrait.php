<?php

namespace Dhii\Iterator;

use Dhii\Util\String\StringableInterface as Stringable;
use Traversable;

/**
 * Common functionality for objects that can create recursive iteration instances.
 *
 * @since [*next-version*]
 */
trait CreateRecursiveIterationCapableTrait
{
    /**
     * Creates a new recursive iteration instance.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null            $key          The iteration key.
     * @param mixed                             $value        The iteration value.
     * @param string[]|Stringable[]|Traversable $pathSegments The segments of the path to the iteration.
     *
     * @return RecursiveIteration The created instance.
     */
    protected function _createRecursiveIteration($key, $value, $pathSegments = [])
    {
        return new RecursiveIteration($key, $value, $pathSegments);
    }
}
