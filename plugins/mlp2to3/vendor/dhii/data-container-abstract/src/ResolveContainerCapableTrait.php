<?php

namespace Dhii\Data\Container;

use Psr\Container\ContainerInterface as BaseContainerInterface;

/**
 * Functionality for resolving the inner-most container from a container chain.
 *
 * @since [*next-version*]
 */
trait ResolveContainerCapableTrait
{
    /**
     * Resolves the inner-most container of a container chain.
     *
     * Will recursively try and retrieve the assigned container, until reaching a non-container-aware container,
     * or an inner container that is null, in which case the outer container is returned.
     *
     * @since [*next-version*]
     *
     * @param BaseContainerInterface|ContainerAwareInterface $container The optionally container-aware container to resolve.
     *
     * @return BaseContainerInterface The inner-most container.
     */
    protected function _resolveContainer(BaseContainerInterface $container)
    {
        $parent = null;

        while ($container instanceof ContainerAwareInterface) {
            $parent = $container->getContainer();

            if (!($parent instanceof BaseContainerInterface)) {
                break;
            }
            $container = $parent;
        }

        return $container;
    }
}
