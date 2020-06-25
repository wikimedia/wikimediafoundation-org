<?php

namespace Dhii\Data\Container\Exception;

use Dhii\Exception\AbstractBaseException;
use Dhii\Data\Container\ContainerAwareTrait;

/**
 * Common functionality for container exceptions.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseContainerException extends AbstractBaseException
{
    /*
     * Adds container awareness.
     *
     * @since [*next-version*]
     */
    use ContainerAwareTrait;

    /**
     * Parameter-less constructor.
     *
     * Invoke this in the actual constructor.
     *
     * @since [*next-version*]
     */
    protected function _construct()
    {
    }
}
