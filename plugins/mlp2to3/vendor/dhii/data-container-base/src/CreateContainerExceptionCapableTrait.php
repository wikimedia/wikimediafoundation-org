<?php

namespace Dhii\Data\Container;

use Psr\Container\ContainerInterface as BaseContainerInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Dhii\Data\Container\Exception\ContainerException;
use Exception as RootException;

trait CreateContainerExceptionCapableTrait
{
    /**
     * Creates a new container exception.
     *
     * @param string|Stringable|null      $message   The exception message, if any.
     * @param int|string|Stringable|null  $code      The numeric exception code, if any.
     * @param RootException|null          $previous  The inner exception, if any.
     * @param BaseContainerInterface|null $container The associated container, if any.
     *
     * @since [*next-version*]
     *
     * @return ContainerException The new exception.
     */
    protected function _createContainerException(
        $message = null,
        $code = null,
        RootException $previous = null,
        BaseContainerInterface $container = null
    ) {
        return new ContainerException($message, $code, $previous, $container);
    }
}
