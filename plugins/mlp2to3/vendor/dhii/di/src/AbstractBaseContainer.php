<?php

namespace Dhii\Di;

use Dhii\Data\Container\NormalizeContainerCapableTrait;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Psr\Container\ContainerExceptionInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Dhii\Data\Container\AbstractBaseContainer as BaseAbstractBaseContainer;

/**
 * Common functionality for regular DI containers.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseContainer extends BaseAbstractBaseContainer
{
    /* Data object methods.
     *
     * @since [*next-version*]
     */
    use DataObjectTrait;

    /* Ability to normalize into an array.
     *
     * @since [*next-version*]
     */
    use NormalizeArrayCapableTrait;

    /* Ability to normalize into a container.
     *
     * @since [*next-version*]
     */
    use NormalizeContainerCapableTrait;

    /* Ability to normalize into a string.
     *
     * @since [*next-version*]
     */
    use NormalizeStringCapableTrait;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function get($key)
    {
        return $this->_getService($key);
    }

    /**
     * Throws a container exception.
     *
     * @param string|Stringable|null      $message   The exception message, if any.
     * @param int|string|Stringable|null  $code      The numeric exception code, if any.
     * @param RootException|null          $previous  The inner exception, if any.
     * @param BaseContainerInterface|null $container The associated container, if any. Pass `true` to use available container.
     *
     * @throws ContainerExceptionInterface
     */
    protected function _throwContainerException($message = null, $code = null, $previous = null, $container = null)
    {
        $container = $container === true
            ? $this
            : $container;

        throw $this->_createContainerException($message, $code, $previous, $container);
    }

    /**
     * Retrieves a service by key.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key The key, for which to get the service.
     *
     * @throw NotFoundExceptionInterface If no service or definition found for key.
     * @throw ContainerExceptionInterface If service or service definition could not be retrieved.
     *
     * @return mixed The corresponding service.
     */
    abstract protected function _getService($key);
}
