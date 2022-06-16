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

namespace Inpsyde\MultilingualPress\Framework\Setting\Site;

/**
 * Interface for all site settings section view model implementations.
 */
interface SiteSettingsSectionViewModel
{

    /**
     * Returns the ID of the site settings section.
     *
     * @return string
     */
    public function id(): string;

    /**
     * Returns the markup for the site settings section.
     *
     * @param int $siteId
     * @return bool
     */
    public function renderView(int $siteId): bool;

    /**
     * Returns the title of the site settings section.
     *
     * @return string
     */
    public function title(): string;
}
