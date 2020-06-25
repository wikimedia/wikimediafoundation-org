<?php

namespace Dhii\Di;

use ArrayAccess;
use stdClass;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Dhii\Data\Container\ContainerAwareInterface;
use Dhii\Data\Container\ContainerAwareTrait;
use Dhii\Data\Container\ResolveContainerCapableTrait;
use Dhii\Exception\CreateInvalidArgumentExceptionCapableTrait;
use Dhii\Exception\CreateOutOfRangeExceptionCapableTrait;
use Dhii\Exception\CreateRuntimeExceptionCapableTrait;
use Dhii\Exception\CreateInternalExceptionCapableTrait;
use Dhii\Invocation\CreateInvocationExceptionCapableTrait;
use Dhii\Validation\CreateValidationFailedExceptionCapableTrait;
use Dhii\I18n\StringTranslatingTrait;
use Dhii\Cache\ContainerInterface as CacheContainerInterface;
use Dhii\Util\Normalization\NormalizeIntCapableTrait;

/**
 * A DI container that is aware of a parent container.
 *
 * Will resolve callable definitions, and cache the result.
 * While resolving, will retrieve the inner-most container from the
 * ancestor chain, and pass it to the definition.
 *
 * @since [*next-version*]
 */
class ContainerAwareCachingContainer extends AbstractBaseCachingContainer implements ContainerAwareInterface
{
    /* Awareness of an outer container.
     *
     * @since [*next-version*]
     */
    use ContainerAwareTrait;

    /* Ability to resolve inner-most container.
     *
     * @since [*next-version*]
     */
    use ResolveContainerCapableTrait;

    /*
     * Basic ability to i18n strings.
     *
     * @since [*next-version*]
     */
    use StringTranslatingTrait;

    /* Ability to normalize into an integer.
     *
     * @since [*next-version*]
     */
    use NormalizeIntCapableTrait;

    /* Factory of Validation Failed exception.
     *
     * @since [*next-version*]
     */
    use CreateValidationFailedExceptionCapableTrait;

    /* Factory of Runtime exception.
     *
     * @since [*next-version*]
     */
    use CreateRuntimeExceptionCapableTrait;

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

    /* Factory of Invocation exception.
     *
     * @since [*next-version*]
     */
    use CreateInvocationExceptionCapableTrait;

    /* Factory of Internal exception.
     *
     * @since [*next-version*]
     */
    use CreateInternalExceptionCapableTrait;

    /**
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|BaseContainerInterface $services The services container.
     */
    public function __construct($services, CacheContainerInterface $serviceCache, $parentContainer = null)
    {
        if (is_array($services)) {
            $services = (object) $services;
        }

        $this->_setDataStore($services);
        $this->_setServiceCache($serviceCache);
        !is_null($parentContainer) && $this->_setContainer($parentContainer);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getContainer()
    {
        return $this->_getContainer();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _getArgsForDefinition($definition)
    {
        $container = $this->_resolveContainer($this);

        return [$container];
    }
}
