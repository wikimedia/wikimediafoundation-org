<?php

namespace Dhii\Collection;

use ArrayAccess;
use ArrayObject;
use InvalidArgumentException;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use stdClass;

/**
 * Functionality for recursive map factories.
 *
 * @since [*next-version*]
 */
trait RecursiveFactoryTrait
{
    /**
     * Normalizes a map child element.
     *
     * @since [*next-version*]
     *
     * @param mixed                                                  $child  The child to normalize.
     * @param array|ArrayAccess|BaseContainerInterface|stdClass|null $config The config of the product, the child of which to normalize.
     *
     * @throws InvalidArgumentException If the child is not valid.
     *
     * @return mixed The normalized element.
     */
    protected function _normalizeChild($child, $config = null)
    {
        if (is_scalar($child) || is_null($child)) {
            return $this->_normalizeSimpleChild($child, $config);
        }

        return $this->_normalizeComplexChild($child, $config);
    }

    /**
     * Normalizes a non-scalar child.
     *
     * @param object|array|resource                                  $child  The child to normalize
     * @param array|ArrayAccess|BaseContainerInterface|stdClass|null $config The config of the product, the child of which to normalize.
     *
     * @throws InvalidArgumentException If the child is not valid.
     *
     * @return mixed
     */
    protected function _normalizeComplexChild($child, $config = null)
    {
        return $this->_createChildInstance($child, $config);
    }

    /**
     * Creates a new instance of a child element.
     *
     * @since [*next-version*]
     *
     * @param object|array|null                                      $child  The child, for which to create a new instance.
     * @param array|ArrayAccess|BaseContainerInterface|stdClass|null $config The config of the product, the child of which to create an instance for.
     *
     * @return mixed the new child.
     */
    protected function _createChildInstance($child, $config = null)
    {
        $childConfig = $this->_getChildConfig($child, $config);
        $factory     = $this->_getChildFactory($child, $config);

        return $factory->make($childConfig);
    }

    /**
     * Normalizes a scalar child.
     *
     * @since [*next-version*]
     *
     * @param bool|int|float|string|null                             $child  The child to normalize.
     * @param array|ArrayAccess|BaseContainerInterface|stdClass|null $config The config of the product, the child of which to normalize.
     *
     * @return mixed The normalized child.
     */
    abstract protected function _normalizeSimpleChild($child, $config);

    /**
     * Retrieves the factory that is used to create children instances.
     *
     * @since [*next-version*]
     *
     * @param mixed                                                  $child  The child for which to get the factory.
     * @param array|ArrayAccess|BaseContainerInterface|stdClass|null $config The config of the product, the child of which to get the factory for.
     *
     * @return MapFactoryInterface The child factory.
     */
    abstract protected function _getChildFactory($child, $config);

    /**
     * Retrieves configuration that can be used to make a child instance with a factory.
     *
     * @since [*next-version*]
     *
     * @param mixed                                                  $child  The child for which to get the config.
     * @param array|ArrayAccess|BaseContainerInterface|stdClass|null $config The config of the product,
     *                                                                       the child of which the config to get the config for.
     *
     * @return array|stdClass|ArrayObject|BaseContainerInterface The configuration for a child factory.
     */
    abstract protected function _getChildConfig($child, $config);
}
