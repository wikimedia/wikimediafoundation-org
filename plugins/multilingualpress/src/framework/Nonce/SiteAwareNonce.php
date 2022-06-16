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

namespace Inpsyde\MultilingualPress\Framework\Nonce;

interface SiteAwareNonce extends Nonce
{
    /**
     * Make nonce instance specific for a given site.
     *
     * @param int $siteId
     * @return SiteAwareNonce
     */
    public function withSite(int $siteId): SiteAwareNonce;
}
