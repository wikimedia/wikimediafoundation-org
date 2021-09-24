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

namespace Inpsyde\MultilingualPress\Framework\Cache\Item;

use Inpsyde\MultilingualPress\Framework\Cache\Driver\CacheDriver;

/**
 * A complete multi-driver cache item.
 */
final class WpCacheItem implements CacheItem
{
    const DIRTY = 'dirty';
    const DIRTY_SHALLOW = 'shallow';
    const DELETED = 'deleted';
    const CLEAN = '';

    /**
     * @var CacheDriver
     */
    private $driver;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $group;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var bool
     */
    private $isHit = false;

    /**
     * @var string
     */
    private $dirtyStatus = self::CLEAN;

    /**
     * @var bool|null
     */
    private $isExpired;

    /**
     * @var int
     */
    private $timeToLive;

    /**
     * @var \DateTimeImmutable|null
     */
    private $lastSave;

    /**
     * @var bool
     */
    private $shallowUpdate = false;

    /**
     * @param CacheDriver $driver
     * @param string $key
     * @param string $group
     * @param int|null $timeToLive
     */
    public function __construct(
        CacheDriver $driver,
        string $key,
        string $group = '',
        int $timeToLive = null
    ) {

        $this->driver = $driver;
        $this->key = $key;
        $this->group = $group;
        $this->timeToLive = $timeToLive;

        $this->calculateStatus();
    }

    /**
     * Before the object vanishes its storage its updated if needs to.
     */
    public function __destruct()
    {
        $this->syncToStorage();
    }

    /**
     * @inheritdoc
     */
    public function key(): string
    {
        return $this->group . $this->key;
    }

    /**
     * @inheritdoc
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function fillWith($value): bool
    {
        // phpcs:enable

        $this->isHit = true;
        $this->value = $value;
        $this->isExpired = null;
        $this->dirtyStatus = self::DIRTY;
        $this->lastSave = $this->now();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function isHit(): bool
    {
        return $this->isHit;
    }

    /**
     * @inheritdoc
     */
    public function isExpired(): bool
    {
        if (!$this->isHit()) {
            return false;
        }

        if (is_int($this->isExpired)) {
            return $this->isExpired;
        }

        // If we have a last save and a time to live, calculate an expired timestamp based on that.
        $expiryTime = $this->lastSave && is_int($this->timeToLive)
            ? $this->lastSave->getTimestamp() + $this->timeToLive
            : null;

        // If no expiration date, nor we were able to calculate a expiration by TTL, just return.
        if (null === $expiryTime) {
            return false;
        }

        $this->isExpired = $expiryTime < (int)$this->now()->format('U');

        return $this->isExpired;
    }

    /**
     * @inheritdoc
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    public function value()
    {
        // phpcs:enable

        if ($this->isHit) {
            return $this->value;
        }

        if (self::DELETED === $this->dirtyStatus) {
            return null;
        }

        $this->calculateStatus();

        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function delete(): bool
    {
        $this->value = null;
        $this->timeToLive = null;
        $this->lastSave = null;
        $this->isExpired = null;
        $this->isHit = false;
        $this->dirtyStatus = self::DELETED;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function liveFor(int $ttl): CacheItem
    {
        $this->timeToLive = $ttl;
        $this->isExpired = null;

        if (self::CLEAN === $this->dirtyStatus) {
            $this->dirtyStatus = self::DIRTY_SHALLOW;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function syncToStorage(): bool
    {
        if (self::CLEAN === $this->dirtyStatus) {
            return true;
        }

        // Shallow update means no change will be done on "last save" property,
        // so we don't prolong the TTL.
        $this->shallowUpdate = self::DIRTY_SHALLOW === $this->dirtyStatus;
        $updated = $this->update();
        $this->shallowUpdate = false;

        // If the update is successful, the value is in sync with storage, so status is clean again.
        if ($updated) {
            $this->dirtyStatus = self::CLEAN;

            return true;
        }

        // If update failed, the status of the item need to be re-calculated.
        $this->calculateStatus();

        return false;
    }

    /**
     * @inheritdoc
     */
    public function syncFromStorage(): bool
    {
        $this->delete();
        $this->dirtyStatus = self::CLEAN;
        $this->calculateStatus();

        return true;
    }

    /**
     * Initialize (or update) the internal status of the item.
     */
    private function calculateStatus()
    {
        // First, load cached value from storage and mark item as "hit" if the value was actually stored.
        $value = $this->driver->read($this->group, $this->key);

        $cachedValue = $value->value();
        $this->isHit =
            $value->isHit()
            && is_array($cachedValue)
            && $cachedValue;

        // Then initialize properties and override them with values from storage,
        // only if those exists and validates.
        $value = null;
        $ttl = null;
        $lastSave = null;
        if ($this->isHit) {
            list($value, $ttl, $lastSave) = $this->prepareValue($cachedValue);
        }

        // Always override "last save" value from storage if there is something there.
        if ($this->isHit) {
            $this->lastSave = $lastSave;
        }

        // If no value was already set on the item (via `fillWith()`) use value from storage
        // (or null, if no hit).
        if (null === $this->value) {
            $this->value = $value;
        }

        // If no TTL was already set on the item (via `live_for()`) use value from storage
        // (or default, if no hit).
        if (null === $this->timeToLive) {
            $this->timeToLive = is_int($ttl) ? $ttl : self::LIFETIME_IN_SECONDS;
        }

        $currentTtl = $ttl ?? self::LIFETIME_IN_SECONDS;

        /**
         * Calculate status:
         * - properties match storage, the status is "clean" (no update needed)
         * - "value" differs from storage, the status is "dirty" (full update needed)
         * - "value" matches storage, TTL differs, status is "dirty shallow" (partial update needed)
         */
        $this->dirtyStatus = self::CLEAN;
        if ($this->value !== $value) {
            $this->dirtyStatus = self::DIRTY;
        } elseif ($currentTtl !== $this->timeToLive) {
            $this->dirtyStatus = self::DIRTY_SHALLOW;
        }
    }

    /**
     * @return bool
     */
    private function update(): bool
    {
        if ($this->isHit) {
            $this->driver->write(
                $this->group,
                $this->key,
                $this->prepareValue()
            );

            $this->isExpired = null;

            return true;
        }

        return $this->driver->delete($this->group, $this->key);
    }

    /**
     * @return \DateTimeImmutable
     */
    private function now(): \DateTimeInterface
    {
        return new \DateTimeImmutable('now', new \DateTimeZone('GMT'));
    }

    /**
     * Compact to and explode from storage a value.
     *
     * @param array|null $compactValue
     * @return array
     */
    private function prepareValue(array $compactValue = null): array
    {
        if ($compactValue === null) {
            // When shallow update, we don't update last save time, unless value was never saved before.
            $lastSave = (!$this->shallowUpdate || !$this->lastSave)
                ? $this->now()
                : $this->lastSave;

            return [
                'V' => $this->value,
                'T' => (int)$this->timeToLive ?: self::LIFETIME_IN_SECONDS,
                'S' => $this->serializeDate($lastSave),
            ];
        }

        $value = $compactValue['V'] ?? null;
        $ttl = isset($compactValue['T']) ? (int)$compactValue['T'] : null;
        $lastSave = isset($compactValue['S'])
            ? $this->unserializeDate((string)$compactValue['S'])
            : null;

        return [
            $value,
            $ttl,
            $lastSave,
        ];
    }

    /**
     * @param \DateTimeInterface|null $date
     * @return string
     */
    private function serializeDate(\DateTimeInterface $date = null): string
    {
        return $date ? $date->format('c') : $this->now()->format('c');
    }

    /**
     * @param string $date
     * @return \DateTimeImmutable|null
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    private function unserializeDate(string $date)
    {
        // phpcs:enable

        if (!$date) {
            return null;
        }

        $unserialized = \DateTimeImmutable::createFromFormat('U', (string)strtotime($date));

        return $unserialized ?: null;
    }
}
