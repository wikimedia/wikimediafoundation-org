<?php

namespace Dhii\Collection;

use ArrayAccess;
use ArrayObject;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Dhii\Data\Container\ContainerGetCapableTrait;
use Dhii\Data\Container\CreateContainerExceptionCapableTrait;
use Dhii\Data\Container\CreateNotFoundExceptionCapableTrait;
use Dhii\Data\Container\NormalizeKeyCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\Exception\CreateRuntimeExceptionCapableTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use InvalidArgumentException;
use RuntimeException;
use stdClass;

/**
 * A factory of countable maps.
 *
 * @since [*next-version*]
 */
class CountableMapFactory extends AbstractRecursiveMapFactory
{
    /* @since [*next-version*] */
    const PRODUCT_CLASS_NAME = 'Dhii\Collection\CountableMap';

    /* @since [*next-version*] */
    use ContainerGetCapableTrait;

    /* @since [*next-version*] */
    use NormalizeIterableCapableTrait;

    /* @since [*next-version*] */
    use NormalizeKeyCapableTrait;

    /* @since [*next-version*] */
    use NormalizeStringCapableTrait;

    /* @since [*next-version*] */
    use CreateContainerExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateNotFoundExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateRuntimeExceptionCapableTrait;

    /* @since [*next-version*] */
    use CreateOutOfRangeExceptionCapableTrait;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getChildConfig($child, $config)
    {
        return [
            MapFactoryInterface::K_DATA => $child,
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _normalizeSimpleChild($child, $config)
    {
        return $child;
    }

    /**
     * Creates a new factory product.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|BaseContainerInterface|stdClass|null $config The data for the new product instance.
     * @param array|stdClass|ArrayObject                             $data   The data for the new product instance.
     *
     * @throws InvalidArgumentException If the data or the config is invalid.
     * @throws RuntimeException         If the product could not be created.
     *
     * @return mixed The new factory product.
     */
    protected function _createProduct($config, $data)
    {
        $className = static::PRODUCT_CLASS_NAME;

        return new $className($data);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getChildFactory($child, $config)
    {
        return $this;
    }
}
