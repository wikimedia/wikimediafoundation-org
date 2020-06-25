<?php

namespace Dhii\Data;

/**
 * Something that can have a key.
 *
 * A key is an identifier, i.e. a code.
 *
 * @since 0.1
 */
interface KeyAwareInterface
{
    /**
     * Retrieve the key of interface.
     *
     * @since 0.1
     *
     * @return string The key that this interface has.
     */
    public function getKey();
}
