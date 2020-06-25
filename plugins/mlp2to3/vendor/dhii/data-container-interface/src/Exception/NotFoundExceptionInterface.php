<?php

namespace Dhii\Data\Container\Exception;

use Psr\Container\NotFoundExceptionInterface as BaseNotFoundExceptionInterface;
use Dhii\Data\Container\DataKeyAwareInterface;

/**
 * Represents an exception which occurs when data requested for a key is not found.
 *
 * @since 0.1
 */
interface NotFoundExceptionInterface extends
        BaseNotFoundExceptionInterface,
        ContainerExceptionInterface,
        DataKeyAwareInterface
{
}
