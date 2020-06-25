<?php

namespace Dhii\Collection;

use ArrayAccess;
use ArrayObject;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;
use stdClass;
use Traversable;
use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface as BaseContainerInterface;

/**
 * Functionality for map factories.
 *
 * @since [*next-version*]
 */
trait MakeCapableMapTrait
{
    /**
     * Creates a map with the given data.
     *
     * Map representations will be wrapped.
     *
     * @param array|ArrayAccess|BaseContainerInterface|stdClass|null $config The data for the map.
     *
     * @throws RootException If unable to make a new map.
     *
     * @return MapInterface A map containing the data. Each known map representation is also an instance of {@see MapInterface}.
     */
    protected function _make($config)
    {
        try {
            $data = $this->_containerGet($config, MapFactoryInterface::K_DATA);
        } catch (ContainerExceptionInterface $e) {
            throw $this->_createRuntimeException($this->__('Could not retrieve data from factory config'), null, $e);
        }

        try {
            $data = $this->_normalizeIterable($data);
        } catch (InvalidArgumentException $e) {
            throw $this->_createRuntimeException($this->__('Map data must be iterable'), null, $e);
        }

        $map = new stdClass();

        foreach ($data as $_key => $_value) {
            try {
                $map->{$_key} = $this->_normalizeChild($_value, $config);
            } catch (InvalidArgumentException $e) {
                throw $this->_createRuntimeException($this->__('Element "%1$s" is invalid', [$_key]), null, $e);
            }
        }

        $product = $this->_createProduct($config, $map);

        return $product;
    }

    /**
     * Creates a new factory product.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|BaseContainerInterface|stdClass|null $config The config for the new product.
     * @param array|stdClass|ArrayObject                             $data   The data for the new product instance.
     *
     * @throws InvalidArgumentException If the data is invalid.
     * @throws RuntimeException         If the product could not be created.
     *
     * @return mixed The new factory product.
     */
    abstract protected function _createProduct($config, $data);

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
    abstract protected function _normalizeChild($child, $config = null);

    /**
     * Retrieves a value from a container or data set.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|BaseContainerInterface $container The container to read from.
     * @param string|int|float|bool|Stringable                  $key       The key of the value to retrieve.
     *
     * @throws InvalidArgumentException    If container is invalid.
     * @throws ContainerExceptionInterface If an error occurred while reading from the container.
     * @throws NotFoundExceptionInterface  If the key was not found in the container.
     *
     * @return mixed The value mapped to the given key.
     */
    abstract protected function _containerGet($container, $key);

    /**
     * Creates a new child element.
     *
     * @param array|ArrayAccess|BaseContainerInterface|stdClass|null The configuration of the child, if any.
     *
     * @return MapInterface The new child instance.
     */
//    abstract protected function _createChild($config = null);

    /**
     * Normalizes an iterable.
     *
     * @since [*next-version*]
     *
     * @param mixed $iterable The iterable to normalize.
     *
     * @throws InvalidArgumentException If the iterable could not be normalized.
     *
     * @return array|Traversable|stdClass The normalized iterable.
     */
    abstract protected function _normalizeIterable($iterable);

    /**
     * Creates a new Runtime exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     *
     * @return RuntimeException The new exception.
     */
    abstract protected function _createRuntimeException($message = null, $code = null, $previous = null);

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
