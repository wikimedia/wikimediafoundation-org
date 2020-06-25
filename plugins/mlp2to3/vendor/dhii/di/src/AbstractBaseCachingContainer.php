<?php

namespace Dhii\Di;

use Dhii\Invocation\CreateReflectionForCallableCapableTrait;
use Dhii\Iterator\CountIterableCapableTrait;
use Dhii\Iterator\ResolveIteratorCapableTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Common functionality for containers that cache resolved services.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseCachingContainer extends AbstractBaseContainer
{
    /* Ability to retrieve resolved cached service.
     *
     * @since [*next-version*]
     */
    use GetServiceCapableCachingTrait;

    /* Awareness of a service cache.
     *
     * @since [*next-version*]
     */
    use ServiceCacheAwareTrait;

    /* Ability to resolve a service definition.
     *
     * @since [*next-version*]
     */
    use ResolveDefinitionCapableTrait;

    /* Ability to invoke callables;
     *
     * @since [*next-version*]
     */
    use InvokingTrait;

    /* Ability to count iterables.
     *
     * @since [*next-version*]
     */
    use CountIterableCapableTrait;

    /* Ability to resolve an iterator from a Traversable chain.
     *
     * @since [*next-version*]
     */
    use ResolveIteratorCapableTrait;

    /* Ability to normalize into a validator.
     *
     * @since [*next-version*]
     */
    use NormalizeIterableCapableTrait;

    /* Ability to create a reflection for a callable.
     *
     * @since [*next-version*]
     */
    use CreateReflectionForCallableCapableTrait;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getArgsForDefinition($definition)
    {
        return [$this];
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createReflectionFunction($functionName)
    {
        return new ReflectionFunction($functionName);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _createReflectionMethod($className, $methodName)
    {
        return new ReflectionMethod($className, $methodName);
    }
}
