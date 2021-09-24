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
 * Interface for all site setting view implementations.
 */
interface SiteSettingView
{

    /**
     * Renders the site setting markup.
     *
     * @param int $siteId
     * @return bool
     */
    public function render(int $siteId): bool;
}
