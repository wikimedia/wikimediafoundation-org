<?php

namespace Dhii\Data;

/**
 * Something that can represent or have a value.
 *
 * @since 0.1
 */
interface ValueAwareInterface
{
    /**
     * Retrieves the value that this object represents.
     *
     * @since 0.1
     *
     * @return mixed The value that this object represents.
     */
    public function getValue();
}
