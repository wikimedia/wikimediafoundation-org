<?php

namespace Dhii\Di;

use ArrayAccess;
use stdClass;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Dhii\Data\Container\CreateContainerExceptionCapableTrait;
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
 * A basic DI container.
 *
 * Will resolve callable definitions, and cache the result.
 *
 * @since [*next-version*]
 */
class CachingContainer extends AbstractBaseCachingContainer
{
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

    /* Factory of CachingContainer exception.
     *
     * @since [*next-version*]
     */
    use CreateContainerExceptionCapableTrait;

    /**
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|BaseContainerInterface $services     The services container.
     * @param CacheContainerInterface                           $serviceCache The cache for resolved services.
     */
    public function __construct($services, CacheContainerInterface $serviceCache)
    {
        if (is_array($services)) {
            $services = (object) $services;
        }

        $this->_setDataStore($services);
        $this->_setServiceCache($serviceCache);
    }
}
