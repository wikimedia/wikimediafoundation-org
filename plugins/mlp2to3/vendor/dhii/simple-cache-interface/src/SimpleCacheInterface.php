<?php

namespace Dhii\Cache;

use Dhii\Data\Container\ClearCapableInterface;
use Dhii\Data\Container\DeleteCapableInterface;

/**
 * Something that exposes methods for setting, checking for, and removing values in a container for caching purposes.
 *
 * @since [*next-version*]
 */
interface SimpleCacheInterface extends
    ContainerInterface,
    SetCapableInterface,
    ClearCapableInterface,
    DeleteCapableInterface
{
}
