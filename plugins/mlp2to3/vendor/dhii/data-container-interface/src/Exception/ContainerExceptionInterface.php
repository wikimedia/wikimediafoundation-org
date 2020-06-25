<?php

namespace Dhii\Data\Container\Exception;

use Dhii\Data\Container\ContainerAwareInterface;
use Psr\Container\ContainerExceptionInterface as BaseContainerExceptionInterface;
use Dhii\Exception\ThrowableInterface;

/**
 * An exception that occurs in relation to a container.
 *
 * @since 0.1
 */
interface ContainerExceptionInterface extends
        ThrowableInterface,
        ContainerAwareInterface,
        BaseContainerExceptionInterface
{
}
