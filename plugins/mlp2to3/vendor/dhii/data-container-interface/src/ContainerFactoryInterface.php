<?php

namespace Dhii\Data\Container;

use Dhii\Factory\FactoryInterface;
use Psr\Container\ContainerInterface as BaseContainerInterface;

/**
 * Something that can create new container instances.
 *
 * @since 0.2
 */
interface ContainerFactoryInterface extends FactoryInterface
{
    /**
     * The key in the factory config for container data.
     *
     * @since 0.2
     */
    const K_DATA = 'data';

    /**
     * The key in the factory config for service definitions.
     *
     * @since 0.2
     * @deprecated
     */
    const K_CFG_DEFINITIONS = self::K_DATA;

    /**
     * {@inheritdoc}
     *
     * @since 0.2
     *
     * @return BaseContainerInterface The created container instance.
     */
    public function make($config = null);
}
