<?php

namespace Dhii\Di;

use Dhii\Cache\ContainerInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;

/**
 * Functionality for assigning and retrieving a service cache.
 *
 * @since [*next-version*]
 */
trait ServiceCacheAwareTrait
{
    /**
     * The cache of services.
     *
     * @since [*next-version*]
     *
     * @var ContainerInterface|null
     */
    protected $serviceCache;

    /**
     * Retrieves the service cache from this instance.
     *
     * @since [*next-version*]
     *
     * @return ContainerInterface|null The service cache.
     */
    protected function _getServiceCache()
    {
        return $this->serviceCache;
    }

    /**
     * Assigns a service cache to this instance.
     *
     * @since [*next-version*]
     *
     * @param ContainerInterface|null $serviceCache The service cache.
     */
    protected function _setServiceCache($serviceCache)
    {
        if ($serviceCache !== null && !($serviceCache instanceof ContainerInterface)) {
            throw $this->_createInvalidArgumentException($this->__('Invalid cache'), null, null, $serviceCache);
        }

        $this->serviceCache = $serviceCache;
    }

    /**
     * Creates a new Invalid Argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     * @param mixed|null                            $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $argument = null
    );

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
