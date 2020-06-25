<?php

namespace Dhii\Data\Container;

use Psr\Container\ContainerInterface as BaseContainerInterface;

/**
 * A container that can have its members cleared
 *
 * @since [*next-version*]
 */
interface ClearCapableContainerInterface extends
    BaseContainerInterface,
    ClearCapableInterface
{
}
