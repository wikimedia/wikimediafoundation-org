<?php

namespace Dhii\Data\Container;

use Psr\Container\ContainerExceptionInterface;

/**
 * Exposes means of clearing the members of the container.
 *
 * @since 0.2
 */
interface ClearCapableInterface
{
    /**
     * Clears the members.
     *
     * @since 0.2
     *
     * @throws ContainerExceptionInterface If problem clearing.
     */
    public function clear();
}
