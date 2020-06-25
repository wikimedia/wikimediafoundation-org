<?php

namespace Dhii\Data\Container;

use Psr\Container\ContainerInterface as BaseContainerInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Dhii\Data\Container\Exception\NotFoundExceptionInterface;
use Dhii\Data\Container\Exception\ContainerExceptionInterface;

/**
 * Represents something that can have data retrieved by key.
 *
 * @since 0.1
 */
interface ContainerInterface extends
        HasCapableInterface,
        BaseContainerInterface
{
    /**
     * Retrieves something that corresponds to the key.
     *
     * @since 0.1
     *
     * @param string|Stringable $key The key to retrieve the data for.
     *
     * @throws NotFoundExceptionInterface  If data for key is not found.
     * @throws ContainerExceptionInterface If a problem occurs while retrieving.
     */
    public function get($key);
}
