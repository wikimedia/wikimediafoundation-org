<?php
/**
 * ConfigAwareTrait trait.
 *
 * @package MultilingualPress2to3
 */

namespace Inpsyde\MultilingualPress2to3\Config;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 * Functionality for awareness of configuration via a container.
 *
 * @package SoulCodes
 */
trait ConfigAwareTrait
{

    /**
     * The container of services and configuration used by the plugin.
     *
     * @var ContainerInterface
     */
    protected $config;

    /**
     * Retrieves a config value.
     *
     * @param string $key The key of the config value to retrieve.
     *
     * @throws NotFoundExceptionInterface If config for the specified key is not found.
     * @throws ContainerExceptionInterface If problem retrieving config.
     *
     * @return mixed The config value.
     */
    protected function _getConfig($key)
    {
        return $this->_getConfigContainer()->get($key);
    }

    /**
     * Checks whether configuration for the specified key exists.
     *
     * @param string $key The key to check the configuration for.
     *
     * @throws ContainerExceptionInterface If problem checking.
     *
     * @return bool True if config for the specified key exists; false otherwise.
     */
    protected function _hasConfig($key)
    {
        return $this->_getConfigContainer()->has($key);
    }

    /**
     * Assigns a configuration container for this instance.
     *
     * @param ContainerInterface $container The container that holds configuration.
     */
    protected function _setConfigContainer(ContainerInterface $container)
    {
        $this->config = $container;
    }

    /**
     * Retrieves the configuration container for this instance.
     *
     * @return ContainerInterface The container that holds configuration.
     */
    protected function _getConfigContainer()
    {
        return $this->config;
    }
}
