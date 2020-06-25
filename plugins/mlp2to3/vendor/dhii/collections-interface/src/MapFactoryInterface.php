<?php

namespace Dhii\Collection;

use Dhii\Data\Container\ContainerFactoryInterface;

/**
 * A factory that can create maps.
 *
 * @since [*next-version*]
 */
interface MapFactoryInterface extends ContainerFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @return MapInterface The new map.
     */
    public function make($config = null);
}
