<?php

namespace Dhii\Data\Container;

/**
 * Represents a container with complete read and write access to individual elements.
 *
 * @package Dhii\Data\Container
 */
interface WritableContainerInterface extends
    SetCapableContainerInterface,
    DeleteCapableContainerInterface
{
}