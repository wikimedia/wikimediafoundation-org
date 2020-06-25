<?php

namespace Dhii\Data\Container;

use Psr\Container\ContainerInterface as BaseContainerInterface;

/**
 * Something that can have a container retrieved.
 *
 * @since 0.1
 */
interface ContainerAwareInterface
{
    /**
     * Retrieves the container associated with this instance.
     *
     * @since 0.1
     *
     * @return BaseContainerInterface|null The container, if any.
     */
    public function getContainer();
}
