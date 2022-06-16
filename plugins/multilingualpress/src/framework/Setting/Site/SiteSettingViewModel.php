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
 * Interface for all site setting view model implementations.
 */
interface SiteSettingViewModel
{

    /**
     * Renders the markup for the site setting.
     *
     * @param int $siteId
     */
    public function render(int $siteId);

    /**
     * Returns the title of the site setting.
     *
     * @return string
     */
    public function title(): string;
}
