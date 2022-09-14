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

namespace Inpsyde\MultilingualPress\Module\Redirect;

/**
 * Interface for all noredirect storage implementations.
 */
interface NoRedirectStorage
{
    const FILTER_LIFETIME = 'multilingualpress.noredirect_storage_lifetime';
    const LIFETIME_IN_SECONDS = 5 * MINUTE_IN_SECONDS;
    const KEY = 'noredirect';

    /**
     * Adds the given language to the storage.
     *
     * Returns false if language is not actually added, e.g it was already added.
     *
     * @param string $language
     * @return bool
     */
    public function addLanguage(string $language): bool;

    /**
     * Checks if the given language has been stored before.
     *
     * @param string $language
     * @return bool
     */
    public function hasLanguage(string $language): bool;
}
