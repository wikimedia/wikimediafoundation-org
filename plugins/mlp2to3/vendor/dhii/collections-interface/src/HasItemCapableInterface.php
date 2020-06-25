<?php

namespace Dhii\Collection;

use RuntimeException;

/**
 * Something that can check for the existence of an item.
 *
 * @since [*next-version*]
 */
interface HasItemCapableInterface
{
    /**
     * Checks whether this instance has the given item.
     *
     * @since [*next-version*]
     *
     * @param mixed $item The item to check for.
     *
     * @throws RuntimeException If the existence of the item could not be verified.
     *
     * @return bool True if the item exists; false otherwise.
     */
    public function hasItem($item);
}
