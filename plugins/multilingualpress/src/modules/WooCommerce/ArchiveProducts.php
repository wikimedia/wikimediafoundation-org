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

namespace Inpsyde\MultilingualPress\Module\WooCommerce;

/**
 * Class ArchiveProductsUrlFilter
 */
class ArchiveProducts
{
    /**
     * Retrieve the translated shop page archive url
     *
     * @param string $url
     * @return string
     */
    public function shopArchiveUrl(string $url): string
    {
        if (!is_shop()) {
            return $url;
        }

        $shopPageId = wc_get_page_id('shop');
        if (!$shopPageId || $shopPageId < 0) {
            return $url;
        }

        $url = get_permalink($shopPageId);

        if (
            'publish' !== get_post_status($shopPageId)
            && !current_user_can('edit_post', $shopPageId)
        ) {
            return '';
        }

        return (string)$url;
    }
}
