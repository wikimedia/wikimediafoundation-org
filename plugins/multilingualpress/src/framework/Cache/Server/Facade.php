<?php

# -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Framework\Cache\Server;

use Inpsyde\MultilingualPress\Framework\Cache\Exception;

use const Inpsyde\MultilingualPress\ACTION_ACTIVATION;

class Facade
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var bool
     */
    private $claiming = false;

    /**
     * @param Server $server
     * @param string $namespace
     */
    public function __construct(Server $server, string $namespace)
    {
        $this->server = $server;
        $this->namespace = $namespace;
    }

    /**
     * Wrapper for server get.
     *
     * @param string $key
     * @param mixed $args
     * @return mixed
     * @throws Exception\NotRegisteredCacheItem
     * @throws Exception\InvalidCacheArgument
     * @throws Exception\InvalidCacheDriver
     *
     * @see Server::claim()
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     */
    public function claim(string $key, ...$args)
    {
        // phpcs:enable

        if (did_action(ACTION_ACTIVATION)) {
            return null;
        }

        // Prevent loops
        if ($this->claiming) {
            return null;
        }

        $this->claiming = true;

        $value = $this->server->claim($this->namespace, $key, $args);

        $this->claiming = false;

        return $value;
    }

    /**
     * Wrapper for server flush.
     *
     * @param string $key
     * @param array|null $args
     * @return bool
     * @throws Exception\NotRegisteredCacheItem
     * @throws Exception\InvalidCacheDriver
     *
     * @see Server::flush()
     */
    public function flush(string $key, array $args = null): bool
    {
        return $this->server->flush($this->namespace, $key, $args);
    }
}
