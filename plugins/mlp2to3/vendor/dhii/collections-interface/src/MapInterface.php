<?php

namespace Dhii\Collection;

use Psr\Container\ContainerInterface;
use Traversable;

/**
 * A traversable container.
 *
 * @since [*next-version*]
 */
interface MapInterface extends
    /* @since [*next-version*] */
    Traversable,
    /* @since [*next-version*] */
    ContainerInterface
{
}
