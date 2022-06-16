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

namespace Inpsyde\MultilingualPress\Framework\Setting;

interface SiteSettingsUpdatable
{
    /**
     * Defines the initial settings of a new site.
     *
     * @param int $siteId
     */
    public function defineInitialSettings(int $siteId);

    /**
     * Updates the settings of an existing site.
     *
     * @param int $siteId
     */
    public function updateSettings(int $siteId);
}
