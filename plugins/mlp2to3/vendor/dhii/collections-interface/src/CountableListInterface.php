<?php

namespace Dhii\Collection;

use Traversable;
use Countable;

/**
 * A list that can be counted.
 *
 * @since [*next-version*]
 */
interface CountableListInterface extends
    /* @since [*next-version*] */
    Traversable,
    /* @since [*next-version*] */
    Countable
{
}
