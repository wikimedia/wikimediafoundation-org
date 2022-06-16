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

namespace Inpsyde\MultilingualPress\Module\LanguageSwitcher;

class ItemFactory
{
    /**
     * @param string $languageName
     * @param string $locale
     * @param string $isoCode
     * @param string $flag
     * @param string $url
     * @param int $siteId
     * @return Item
     */
    public function create(
        string $languageName,
        string $locale,
        string $isoCode,
        string $flag,
        string $url,
        int $siteId
    ): Item {

        return new Item($languageName, $locale, $isoCode, $flag, $url, $siteId);
    }
}
