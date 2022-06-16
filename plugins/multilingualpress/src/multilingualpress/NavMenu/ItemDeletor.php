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

namespace Inpsyde\MultilingualPress\NavMenu;

use Inpsyde\MultilingualPress\Framework\NetworkState;

/**
 * Deletes nav menu items.
 */
class ItemDeletor
{
    /**
     * @var \wpdb
     */
    private $wpdb;

    /**
     * @param \wpdb $wpdb
     */
    public function __construct(\wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    /**
     * Deletes all remote MultilingualPress nav menu items linking to the (to-be-deleted) site with
     * the given ID.
     *
     * @param \WP_Site $oldSite
     * @return int
     */
    public function deleteItemsForDeletedSite(\WP_Site $oldSite): int
    {
        $deletedSiteId = (int)$oldSite->blog_id;
        if ($deletedSiteId < 1) {
            return 0;
        }

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $sitesSql = $this->wpdb->prepare(
            "SELECT blog_id FROM {$this->wpdb->blogs} WHERE blog_id != %d",
            $deletedSiteId
        );

        $sites = $this->wpdb->get_col($sitesSql);
        if (!$sites) {
            return 0;
        }

        $deleted = 0;
        $networkState = NetworkState::create();
        foreach ($this->wpdb->get_col($sitesSql) as $siteId) {
            switch_to_blog($siteId);

            $postSqlFormat = "SELECT p.ID FROM {$this->wpdb->posts} p ";
            $postSqlFormat .= "INNER JOIN {$this->wpdb->postmeta} pm ON p.ID = pm.post_id ";
            $postSqlFormat .= 'WHERE pm.meta_key = %s AND pm.meta_value = %s';

            $postSql = $this->wpdb->prepare(
                $postSqlFormat,
                ItemRepository::META_KEY_SITE_ID,
                $deletedSiteId
            );

            foreach ($this->wpdb->get_col($postSql) as $postId) {
                wp_delete_post($postId, true) and $deleted++;
            }
        }
        // phpcs:enable

        $networkState->restore();

        return $deleted;
    }
}
