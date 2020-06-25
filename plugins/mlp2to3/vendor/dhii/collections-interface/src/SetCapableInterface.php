<?php

namespace Dhii\Collection;

use RuntimeException;

/**
 * Something that can set a value for a key.
 *
 * @since [*next-version*]
 */
interface SetCapableInterface
{
    /**
     * Sets a value for a key.
     *
     * @since [*next-version*]
     *
     * @param string $key   The key to set the value for.
     * @param mixed  $value The value to set.
     *
     * @throws RuntimeException If the value cannot be set.
     */
    public function set($key, $value);
}
