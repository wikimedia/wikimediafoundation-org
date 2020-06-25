<?php

namespace Dhii\Di;

use Dhii\Invocation\Exception\InvocationExceptionInterface;
use Dhii\Exception\InternalExceptionInterface;
use InvalidArgumentException;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use stdClass;
use Traversable;

/**
 * Functionality for assigning a container for a service definition.
 *
 * @since [*next-version*]
 */
trait AssignDefinitionContainerCapableTrait
{
    /**
     * Wraps the given definition such that it will receive the given container.
     *
     * The definition will receive the `$container` no matter what container it is invoked with.
     * All other arguments to the definition will be preserved.
     * This is very useful in converting existing definitions to use a different container,
     * possibly a composite one.
     *
     * @since [*next-version*]
     *
     * @param callable               $definition The definition to assign the container to.
     * @param BaseContainerInterface $container  The container to assign.
     *
     * @return callable A definition that will be invoked with the given container.
     */
    protected function _assignDefinitionContainer(callable $definition, BaseContainerInterface $container)
    {
        return function (BaseContainerInterface $c) use ($definition, $container) {
            $args    = func_get_args();
            $args[0] = $container;

            return $this->_invokeCallable($definition, $args);
        };
    }

    /**
     * Invokes a callable.
     *
     * @since [*next-version*]
     *
     * @param callable                   $callable The callable to invoke.
     * @param array|Traversable|stdClass $args     The arguments to invoke the callable with.
     *
     * @throws InvalidArgumentException     If the callable is not callable.
     * @throws InvalidArgumentException     If the args are not a valid list.
     * @throws InvocationExceptionInterface If the callable cannot be invoked.
     * @throws InternalExceptionInterface   If a problem occurs during invocation.
     *
     * @return
     */
    abstract protected function _invokeCallable($callable, $args);
}
