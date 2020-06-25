<?php

namespace Dhii\Data\Container;

use Exception as RootException;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Abstract base functionality of a container.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseContainer implements ContainerInterface
{
    /* Not Found exception factory.
     *
     * @since [*next-version*]
     */
    use CreateNotFoundExceptionCapableTrait;

    /* Container exception factory.
     *
     * @since [*next-version*]
     */
    use CreateContainerExceptionCapableTrait;

    /**
     * Parameter-less constructor.
     *
     * This is run after the instance has been initialized.
     */
    protected function _construct()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function get($key)
    {
        return $this->_get($key);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function has($key)
    {
        return $this->_has($key);
    }

    /**
     * Gets a value from this container by key.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key The key, for which to get the data.
     *
     * @throws NotFoundExceptionInterface  If the key was not found in the container.
     * @throws ContainerExceptionInterface If data could not be retrieved from the container.
     *
     * @return mixed The value for the specified key.
     */
    protected function _get($key)
    {
        try {
            return $this->_getData($key);
        } catch (NotFoundExceptionInterface $e) {
            throw $this->_createNotFoundException($this->__('Key "%1$s" not found', array($key)), null, $e, $this, $key);
        } catch (RootException $e) {
            throw $this->_createContainerException($this->__('Could not retrieve value for key "%1$s"', array($key)), null, $e, $this);
        }
    }

    /**
     * Checks for a key on this container.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key The key, for which to get the data.
     *
     * @throws ContainerExceptionInterface If data could not be retrieved from the container.
     *
     * @return bool True if this container has the specified key; false otherwise.
     */
    protected function _has($key)
    {
        try {
            return $this->_hasData($key);
        } catch (RootException $e) {
            throw $this->_createContainerException($this->__('Could not check for key "%1$s"', array($key)), null, $e, $this);
        }
    }

    /**
     * Retrieve a value by key.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key The key, for which to get the data.
     *                                              Unless an integer is given, this will be normalized to string.
     *
     * @throws InvalidArgumentException    If key is invalid.
     * @throws ContainerExceptionInterface If an error occurred while reading from the container.
     * @throws NotFoundExceptionInterface  If the key was not found in the container.
     *
     * @return mixed The value for the specified key.
     */
    abstract protected function _getData($key);

    /**
     * Check data by key.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key The key, for which to check the data.
     *                                              Unless an integer is given, this will be normalized to string.
     *
     * @throws InvalidArgumentException    If key is invalid.
     * @throws ContainerExceptionInterface If an error occurred while reading from the container.
     *
     * @return bool True if data for the specified key exists; false otherwise.
     */
    abstract protected function _hasData($key);

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
    abstract protected function __($string, $args = array(), $context = null);
}
