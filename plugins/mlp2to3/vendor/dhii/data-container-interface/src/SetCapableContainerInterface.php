<?php

namespace Dhii\Data\Container;

use Psr\Container\ContainerInterface as BaseContainerInterface;

/**
 * A container that can have values set.
 */
interface SetCapableContainerInterface extends
    BaseContainerInterface,
    SetCapableInterface
{
}
