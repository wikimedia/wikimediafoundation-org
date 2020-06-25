<?php

namespace Dhii\Di;

use Dhii\Cache\ContainerInterface as CacheContainerInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use RuntimeException;

/**
 * Functionality for service retrieval.
 *
 * @since [*next-version*]
 */
trait GetServiceCapableCachingTrait
{
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
    protected function _getService($key)
    {
        $cache             = $this->_getServiceCache();
        $notFoundException = null;

        try {
            return $cache->get($key, function ($key) use (&$notFoundException) {
                try {
                    $definition = $this->_get($key);
                } catch (NotFoundExceptionInterface $e) {
                    $notFoundException = $e;
                    throw $e;
                }

                 return $this->_resolveDefinition($definition);
            });
        } catch (RootException $e) {
            if ($notFoundException instanceof NotFoundExceptionInterface) {
                throw $notFoundException;
            }

            throw $this->_throwContainerException($this->__('Could not retrieve service'), null, $e, true);
        }
    }

    /**
     * Gets a service definition.
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $key The key, for which to get the definition.
     *
     * @throws NotFoundExceptionInterface  If the key was not found in the container.
     * @throws ContainerExceptionInterface If data could not be retrieved from the container.
     *
     * @return mixed The definition for the specified key.
     */
    abstract protected function _get($key);

    /**
     * Retrieves the service cache.
     *
     * @since [*next-version*]
     *
     * @return CacheContainerInterface The cached.
     */
    abstract protected function _getServiceCache();

    /**
     * Resolves a service definition.
     *
     * Resolving a definition means acquiring an instance of a service according to its definition.
     *
     * @since [*next-version*]
     *
     * @param mixed $definition The service definition.
     *
     * @throws RuntimeException If the definition cannot be resolved.
     *
     * @return mixed The resolved service.
     */
    abstract protected function _resolveDefinition($definition);

    /**
     * Throws a container exception.
     *
     * @param string|Stringable|null           $message   The exception message, if any.
     * @param int|string|Stringable|null       $code      The numeric exception code, if any.
     * @param RootException|null               $previous  The inner exception, if any.
     * @param BaseContainerInterface|true|null $container The associated container, if any. Pass `true` to use available container.
     *
     * @throws ContainerExceptionInterface
     */
    abstract protected function _throwContainerException($message = null, $code = null, $previous = null, $container = null);

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
