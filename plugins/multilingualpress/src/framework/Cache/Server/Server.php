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

use Inpsyde\MultilingualPress\Framework\Cache\CacheFactory;
use Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver;
use Inpsyde\MultilingualPress\Framework\Cache\Exception;
use Inpsyde\MultilingualPress\Framework\Cache\Pool\CachePool;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;

use function Inpsyde\MultilingualPress\callExit;

class Server
{

    const UPDATING_KEYS_TRANSIENT = 'mlp_cache_server_updating_keys';
    const SPAWNING_KEYS_TRANSIENT = 'mlp_cache_server_spawning_keys_';
    const HEADER_KEY = 'Mlp-Cache-Update-Key';
    const HEADER_TTL = 'Mlp-Cache-Update-TTL';

    const VALID_ARG_TYPES = [
        'boolean',
        'integer',
        'double',
        'string',
    ];

    /**
     * @var string[]
     */
    private static $networkKeys = [];

    /**
     * @var string[]
     */
    private static $siteKeys = [];

    /**
     * @var CacheFactory
     */
    private $factory;

    /**
     * @var CacheDriver
     */
    private $driver;

    /**
     * @var CacheDriver
     */
    private $networkDriver;

    /**
     * @var array[]
     */
    private $registered = [];

    /**
     * @var string[]
     */
    private $spawnQueue = [];

    /**
     * @var bool
     */
    private $inSpawnQueue = false;

    /**
     * @var ServerRequest
     */
    private $request;

    /**
     * @param CacheFactory $factory
     * @param CacheDriver $driver
     * @param CacheDriver $networkDriver
     * @param ServerRequest $request
     */
    public function __construct(
        CacheFactory $factory,
        CacheDriver $driver,
        CacheDriver $networkDriver,
        ServerRequest $request
    ) {

        $this->factory = $factory;
        $this->driver = $driver;
        $this->networkDriver = $networkDriver;
        $this->request = $request;
    }

    /**
     * On regular requests it is possible to register a callback to generates same
     * value to cache and associate it with an unique key in the also given pool.
     * This should be called early, because the values can only be then "claimed"
     * (which is retrieved for actual use) after registration.
     *
     * The value generated will be valid for the given TTL or for the default (1 hour).
     * When value is expired it will be returned anyway, but an update will be scheduled.
     * The scheduled updates happens in separate HEAD requests.
     *
     * It means that once cached for first time a value will be served always from
     * cache (unless manually flushed) and updated automatically on expiration
     * without affecting user request time.
     *
     * @param ItemLogic $itemLogic
     * @return Server
     * @throws Exception\BadCacheItemRegistration
     */
    public function register(ItemLogic $itemLogic): self
    {
        return $this->doRegister($itemLogic, false);
    }

    /**
     * @param ItemLogic $itemLogic
     * @return Server
     * @throws Exception\BadCacheItemRegistration
     */
    public function registerForNetwork(ItemLogic $itemLogic): self
    {
        return $this->doRegister($itemLogic, true);
    }

    /**
     * Check whether the given pair of namespace and key is registered.
     *
     * @param string $namespace
     * @param string $logicKey
     * @return bool
     */
    public function isRegistered(string $namespace, string $logicKey): bool
    {
        return array_key_exists(
            $this->registeredKey($namespace, $logicKey),
            $this->registered
        );
    }

    /**
     * @param string $namespace
     * @param string $logicKey
     * @return CachePool
     * @throws Exception\NotRegisteredCacheItem
     * @throws Exception\InvalidCacheDriver
     */
    public function poolForLogic(string $namespace, string $logicKey): CachePool
    {
        $this->bailIfNotRegistered($namespace, $logicKey);

        /**
         * @var bool $isNetwork
         */
        list($isNetwork) = $this->registered[$this->registeredKey($namespace, $logicKey)];

        return $isNetwork
            ? $this->factory->createForNetwork($namespace, $this->networkDriver)
            : $this->factory->create($namespace, $this->driver);
    }

    /**
     * On regular requests returns the cached (or just newly generated) value for
     * a registered couple of namespace and key.
     * In case the value is expired, it will be returned anyway, but an updating
     * will be scheduled and will happen in a separate HEAD request and the
     * expired cached value will continue to be served until the value is
     * successfully updated.
     *
     * @param string $namespace
     * @param string $key
     * @param array|null $args
     * @return mixed
     * @throws Exception\NotRegisteredCacheItem
     * @throws Exception\InvalidCacheArgument
     * @throws Exception\InvalidCacheDriver
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    public function claim(string $namespace, string $key, array $args = null)
    {
        // phpcs:enable

        $this->bailIfNotRegistered($namespace, $key);
        $this->bailIfBadArgs($args, $namespace, $key);

        $registeredKey = $this->registeredKey($namespace, $key);

        /**
         * @var bool $isNetwork
         * @var ItemLogic $logic
         */
        list($isNetwork, $logic) = $this->registered[$registeredKey];

        $itemKey = $args ? $logic->generateItemKey(...$args) : $logic->key();

        $pool = $this->poolForLogic($namespace, $key);
        $item = $pool->item($itemKey);

        if ($item->isExpired() && !$this->isQueuedForUpdate($namespace, $key, $args)) {
            $this->queueUpdate(
                $registeredKey,
                $namespace . $itemKey,
                $logic->timeToLive(),
                $isNetwork,
                $args
            );
        }

        if ($item->isHit()) {
            return $item->value();
        }

        list($newValue, $success) = $this->fetchUpdatedValue($logic->updater(), $args);

        if ($success) {
            $item->liveFor($logic->timeToLive())->fillWith($newValue);
        }

        return $newValue;
    }

    /**
     * Once cached for first time values continue to be served from cache
     * (automatically updated on expiration) unless this method is called to
     * force flush of a specific namespace / key pair or of a whole namespace.
     *
     * @param string $namespace
     * @param string $key
     * @param array|null $args
     * @return bool
     * @throws Exception\NotRegisteredCacheItem
     * @throws Exception\InvalidCacheDriver
     */
    public function flush(string $namespace, string $key, array $args = null): bool
    {
        if (!$this->isRegistered($namespace, $key)) {
            return false;
        }

        /** @var ItemLogic $logic */
        list(, $logic) = $this->registered[$this->registeredKey($namespace, $key)];
        $pool = $this->poolForLogic($namespace, $key);

        $itemKey = $args ? $logic->generateItemKey(...$args) : $logic->key();
        $fullKey = $namespace . $itemKey;

        $deleted = $pool->delete($itemKey);
        if ($deleted) {
            $pool->item($itemKey)->syncToStorage();
            $this->spawnQueue = array_diff($this->spawnQueue, [$fullKey]);

            return true;
        }

        return false;
    }

    /**
     * When an expired value is requested, it is returned to claiming code, and
     * an HTTP HEAD request is sent to home page containing headers with
     * information about key and the TTL.
     * This methods check them and if the request fits criteria update the value
     * using the registered callable.
     */
    public function listenSpawn()
    {
        list($isCacheRequest, $registeredKey, $args) = $this->updatingRequestData();
        if (!$isCacheRequest || empty($this->registered[$registeredKey])) {
            return;
        }

        /** @var ItemLogic $logic */
        list($isNetwork, $logic) = $this->registered[$registeredKey];
        $namespace = $logic->namespace();
        $logicKey = $logic->key();

        $itemKey = $args ? $logic->generateItemKey(...$args) : $logic->key();
        $updatingKey = md5($namespace . $itemKey);

        if ($this->isUpdating($updatingKey, $isNetwork)) {
            callExit();

            return;
        }

        $this->markUpdating($updatingKey, $isNetwork);

        $itemExtensionOnFailure = $logic->extensionOnFailure();

        /** @var ItemLogic $logic */
        list(, $logic) = $this->registered[$this->registeredKey($namespace, $logicKey)];
        $itemKey = $args ? $logic->generateItemKey(...$args) : $logic->key();

        $item = $this->poolForLogic($namespace, $logicKey)->item($itemKey);

        $item->syncFromStorage();
        $currentValue = $item->value();

        $item = $item->liveFor($logic->timeToLive());
        list($newValue, $success) = $this->fetchUpdatedValue(
            $logic->updater(),
            $args,
            $currentValue
        );

        if (!$success && $itemExtensionOnFailure && $item->isHit()) {
            $item->liveFor($itemExtensionOnFailure)->fillWith($currentValue);
        } elseif (!$success) {
            $item->delete();
        }

        $success and $item->fillWith($newValue);
        $item->syncToStorage();
        $this->markNotUpdating($updatingKey, $isNetwork);

        callExit();
    }

    /**
     * @param string $namespace
     * @param string $key
     * @param array|null $args
     * @return bool
     */
    public function isQueuedForUpdate(string $namespace, string $key, array $args = null): bool
    {
        /** @var ItemLogic $logic */
        list(, $logic) = $this->registered[$this->registeredKey($namespace, $key)];
        $key = $args ? $logic->generateItemKey(...$args) : $logic->key();

        return array_key_exists(
            $namespace . $key,
            $this->spawnQueue
        );
    }

    /**
     * @param string $namespace
     * @param string $key
     * @return string
     */
    private function registeredKey(string $namespace, string $key): string
    {
        return $this->factory->prefix() . $namespace . $key;
    }

    /**
     * Adds the actions that will cause item flushing.
     *
     * @param ItemLogic $logic
     * @param bool $forNetwork
     * @return Server
     * @throws Exception\BadCacheItemRegistration If called during shutdown on in a update request.
     */
    private function doRegister(ItemLogic $logic, bool $forNetwork): self
    {
        if (doing_action('shutdown') || $this->updatingRequestData()[0]) {
            throw Exception\BadCacheItemRegistration::forWrongTiming();
        }

        $registeredKey = $this->registeredKey($logic->namespace(), $logic->key());

        if (
            (!$forNetwork && in_array($registeredKey, self::$networkKeys, true))
            || ($forNetwork && in_array($registeredKey, self::$siteKeys, true))
        ) {
            throw $forNetwork
                ? Exception\BadCacheItemRegistration::forKeyUsedForNetwork($registeredKey)
                : Exception\BadCacheItemRegistration::forKeyUsedForSite($registeredKey);
        }

        $this->registered[$registeredKey] = [$forNetwork, $logic];

        if ($forNetwork && !in_array($registeredKey, self::$networkKeys, true)) {
            self::$networkKeys[] = $registeredKey;
        }

        if (!$forNetwork && !in_array($registeredKey, self::$siteKeys, true)) {
            self::$siteKeys[] = $registeredKey;
        }

        return $this;
    }

    /**
     * Check the HTTP request to see if it is a cache update request.
     * If so return the key and the data plus a flag set to true.
     *
     * @return array
     */
    private function updatingRequestData(): array
    {
        $registeredKey = $this->request->header(self::HEADER_KEY);

        if (!$registeredKey || $this->request->method() !== ServerRequest::PUT) {
            return [false, '', []];
        }

        $body = $this->request->body();
        $args = [];
        if ($body) {
            parse_str($body, $args);
        }

        return [true, $registeredKey, $args ?: null];
    }

    /**
     * Use transients to mark the given key as currently being updated in a
     * update request, to prevent multiple concurrent updates.
     *
     * @param string $key
     * @param bool $isNetwork
     * @return bool
     */
    private function markUpdating(string $key, bool $isNetwork): bool
    {
        $keys = $isNetwork
            ? get_site_transient(self::UPDATING_KEYS_TRANSIENT)
            : get_transient(self::UPDATING_KEYS_TRANSIENT);

        if (!is_array($keys)) {
            $keys = [];
        }

        $keys[] = $key;

        return $isNetwork
            ? set_site_transient(self::UPDATING_KEYS_TRANSIENT, $keys)
            : set_transient(self::UPDATING_KEYS_TRANSIENT, $keys);
    }

    /**
     * Remove the given key from transient storage to mark given key again
     * available for updates.
     *
     * @param string $key
     * @param bool $isNetwork
     * @return bool
     */
    private function markNotUpdating(string $key, bool $isNetwork): bool
    {
        $keys = $isNetwork
            ? get_site_transient(self::UPDATING_KEYS_TRANSIENT)
            : get_transient(self::UPDATING_KEYS_TRANSIENT);

        if (!is_array($keys)) {
            $keys = [];
        }

        unset($keys[$key]);

        return $isNetwork
            ? set_site_transient(self::UPDATING_KEYS_TRANSIENT, $keys)
            : set_transient(self::UPDATING_KEYS_TRANSIENT, $keys);
    }

    /**
     * Use transients to check if the given key is currently being updated in a
     * update request, to prevent multiple concurrent updates.
     *
     * @param string $key
     * @param bool $isNetwork
     * @return bool
     */
    private function isUpdating(string $key, bool $isNetwork): bool
    {
        $keys = $isNetwork
            ? get_site_transient(self::UPDATING_KEYS_TRANSIENT)
            : get_transient(self::UPDATING_KEYS_TRANSIENT);

        return $keys && in_array($key, (array)$keys, true);
    }

    /**
     * Queue given key to be updated in a HTTP request.
     * The first time it is called adds an action on shutdown that will actually
     * process the queue and send updating HTTP requests.
     *
     * @param string $registeredKey
     * @param string $fullKey
     * @param int $timeToLive
     * @param bool $isNetwork
     * @param array $args
     */
    private function queueUpdate(
        string $registeredKey,
        string $fullKey,
        int $timeToLive,
        bool $isNetwork,
        array $args = null
    ) {

        if ($this->inSpawnQueue) {
            return;
        }

        if (!$this->spawnQueue && !did_action('shutdown')) {
            add_action(
                'shutdown',
                function () {
                    $this->inSpawnQueue = true;
                    $this->spawnQueue();
                    $this->spawnQueue = [];
                },
                50
            );
        }

        $this->spawnQueue[$fullKey] = [$registeredKey, $timeToLive, $isNetwork, $args];
    }

    /**
     * Send multiple HTTP request to refresh registered cache items.
     */
    private function spawnQueue(): array
    {
        if (!$this->spawnQueue || !$this->inSpawnQueue) {
            return [];
        }

        $requests = [];
        $keys = ['site' => [], 'network' => []];

        /**
         * @var string $key
         * @var int $ttl
         * @var bool $isNetwork
         */
        foreach ($this->spawnQueue as $fullKey => list($registeredKey, $ttl, $isNetwork, $args)) {
            if ($this->isSpawning($fullKey, $isNetwork)) {
                continue;
            }

            $keys[$isNetwork ? 'network' : 'site'][] = $fullKey;

            $requests[$fullKey] = [
                'url' => home_url(),
                'headers' => [
                    self::HEADER_KEY => $registeredKey,
                    self::HEADER_TTL => $ttl,
                ],
                'data' => is_array($args) ? $args : '',
                'cookies' => [],
                'type' => \Requests::PUT,
            ];
        }

        // phpcs:enable

        $this->markSpawning(true, ...$keys['network']);
        $this->markSpawning(false, ...$keys['site']);

        \Requests::request_multiple(
            $requests,
            [
                'timeout' => 0.01,
                'follow_redirects' => false,
                'blocking' => false,
            ]
        );

        return $requests;
    }

    /**
     * Use transients to mark the given key as currently being sent via an update
     * request, to prevent multiple concurrent request.
     * Transients will not be deleted manually, but are set with a very short
     * expiration so they will expire and vanish in few seconds when (hopefully)
     * all the parallel-executing updating requests finished.
     *
     * @param bool $isNetwork
     * @param string[] $fullKeys
     */
    private function markSpawning(bool $isNetwork, string ...$fullKeys)
    {
        $prefix = self::SPAWNING_KEYS_TRANSIENT;
        foreach ($fullKeys as $key) {
            $isNetwork
                ? set_site_transient($prefix . md5($key), 1, 10)
                : set_transient($prefix . md5($key), 1, 10);
        }
    }

    /**
     * @param string $fullKey
     * @param bool $isNetwork
     * @return bool
     */
    private function isSpawning(string $fullKey, bool $isNetwork): bool
    {
        $prefix = self::SPAWNING_KEYS_TRANSIENT;

        return $isNetwork
            ? (bool)get_site_transient($prefix . md5($fullKey))
            : (bool)get_transient($prefix . md5($fullKey));
    }

    /**
     * @param callable $updater
     * @param array $args
     * @param mixed $currentValue
     * @return array
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    private function fetchUpdatedValue(
        callable $updater,
        array $args = null,
        $currentValue = null
    ): array {

        // phpcs:enable

        is_array($args) or $args = [];

        try {
            $value = $args ? $updater(...$args) : $updater();
            $success = true;
        } catch (\Throwable $throwable) { // phpcs:ignore
            $value = $currentValue;
            $success = false;
        }

        return [$value, $success];
    }

    /**
     * @param string $namespace
     * @param string $key
     * @throws Exception\NotRegisteredCacheItem When required item is not registered.
     */
    private function bailIfNotRegistered(string $namespace, string $key)
    {
        if (!$this->isRegistered($namespace, $key)) {
            throw Exception\NotRegisteredCacheItem::forNamespaceAndKey(
                $namespace,
                $key
            );
        }
    }

    /**
     * @param array|null $args
     * @param string $namespace
     * @param string $key
     * @throws Exception\InvalidCacheArgument When not accepted types are included in cache args
     */
    private function bailIfBadArgs(array $args = null, string $namespace, string $key)
    {
        if ($args === null) {
            return;
        }

        foreach ($args as $arg) {
            if (is_array($arg)) {
                $this->bailIfBadArgs($arg, $namespace, $key);
                continue;
            }

            if (!in_array(gettype($arg), self::VALID_ARG_TYPES, true)) {
                throw Exception\InvalidCacheArgument::forNamespaceAndKey(
                    $namespace,
                    $key
                );
            }
        }
    }
}
