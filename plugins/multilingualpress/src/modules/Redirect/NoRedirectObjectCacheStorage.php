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

namespace Inpsyde\MultilingualPress\Module\Redirect;

/**
 * Object-cache-based noredirect storage implementation.
 *
 * Only used for logged-in users, so they do not mutually affect each other.
 */
final class NoRedirectObjectCacheStorage implements NoRedirectStorage
{

    /**
     * @var string
     */
    private $key;

    /**
     * @inheritdoc
     */
    public function addLanguage(string $language): bool
    {
        $languages = $this->storedLanguages();

        if ($languages && $this->hasLanguage($language)) {
            return false;
        }

        $languages[] = $language;

        wp_cache_set($this->key(), $languages, '', NoRedirectStorage::LIFETIME_IN_SECONDS);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function hasLanguage(string $language): bool
    {
        $languages = $this->storedLanguages();

        return $languages && in_array($language, $languages, true);
    }

    /**
     * Returns the currently stored languages.
     *
     * @return string[]
     */
    private function storedLanguages(): array
    {
        $languages = wp_cache_get($this->key());
        if (!$languages || !is_array($languages)) {
            return [];
        }

        return array_map('strval', $languages);
    }

    /**
     * Returns the cache key for the current user.
     *
     * @return string
     */
    private function key(): string
    {
        if (!$this->key) {
            $this->key = 'multilingualpress.' . NoRedirectStorage::KEY . '.' . get_current_user_id();
        }

        return $this->key;
    }
}
