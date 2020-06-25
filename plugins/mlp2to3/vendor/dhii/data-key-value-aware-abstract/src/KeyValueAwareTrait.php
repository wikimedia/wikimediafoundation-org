<?php

namespace Dhii\Data;

/**
 * Something that has both a key and a value.
 *
 * @since [*next-version*]
 */
trait KeyValueAwareTrait
{
    use KeyAwareTrait;
    use ValueAwareTrait;
}
