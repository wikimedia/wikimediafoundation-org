<?php

namespace Dhii\Cache;

use Dhii\Data\Container\ContainerGetCapableTrait;
use Dhii\Data\Container\ContainerHasCapableTrait;
use Dhii\Data\Container\ContainerUnsetCapableTrait;
use Dhii\Data\Object\CreateDataStoreCapableTrait;
use Dhii\Data\Object\GetDataCapableTrait;
use Dhii\Data\Object\HasDataCapableTrait;
use Dhii\Data\Object\NormalizeKeyCapableTrait;
use Dhii\Data\Object\UnsetDataCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Util\Normalization\NormalizeStringCapableTrait;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerExceptionInterface;
use Exception as RootException;

/**
 * Base functionality for simple cache that stores values in memory.
 *
 * @since [*next-version*]
 */
abstract class AbstractBaseSimpleCacheMemory extends AbstractBaseContainerMemory implements SimpleCacheInterface
{
    /* Factory of datastore object.
     *
     * @since [*next-version*]
     */
    use CreateDataStoreCapableTrait;

    /* Ability to check for an internal data member.
     *
     * @since [*next-version*]
     */
    use HasDataCapableTrait;

    /* Ability to retrieve an internal data member.
     *
     * @since [*next-version*]
     */
    use GetDataCapableTrait;

    /* Ability to unset an internal data member.
     *
     * @since [*next-version*]
     */
    use UnsetDataCapableTrait;

    /* Ability to retrieve a value from a container.
     *
     * @since [*next-version*]
     */
    use ContainerGetCapableTrait;

    /* Ability to check for a value in a container.
     *
     * @since [*next-version*]
     */
    use ContainerHasCapableTrait;

    /* Ability to remove a value from a container.
     *
     * @since [*next-version*]
     */
    use ContainerUnsetCapableTrait;

    /* Basic string i18n.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /* Ability to normalize a container key.
     *
     * @since [*next-version*]
     */
    use NormalizeKeyCapableTrait;

    /* Ability to normalize a string.
     *
     * @since [*next-version*]
     */
    use NormalizeStringCapableTrait;

    /* Factory of Invalid Argument exception.
     *
     * @since [*next-version*]
     */
    use CreateInvalidArgumentExceptionCapableTrait;

    /* Factory of Out of Range exception.
     *
     * @since [*next-version*]
     */
    use CreateOutOfRangeExceptionCapableTrait;

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function clear()
    {
        try {
            $store = $this->_createDataStore();
            $this->_setDataStore($store);
        } catch (RootException $e) {
            throw $this->_createContainerException($this->__('Could not clear data'), null, $e, $this);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key The key to delete the data for.
     */
    public function delete($key)
    {
        try {
            $this->_unsetData($key);
        } catch (RootException $e) {
            throw $this->_createContainerException($this->__('Could not delete data'), null, $e, $this);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param string|int|float|bool|Stringable $key The key to set the data for.
     *
     * @since [*next-version*]
     */
    public function set($key, $value, $ttl = null)
    {
        try {
            $this->_set($key, $value, $ttl);
        } catch (RootException $e) {
            if ($e instanceof ContainerExceptionInterface) {
                throw $e;
            }

            throw $this->_createContainerException($this->__('Could not set data'), null, $e, $this);
        }
    }
}
