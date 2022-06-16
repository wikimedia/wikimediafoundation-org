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

namespace Inpsyde\MultilingualPress\Module\QuickLinks;

use Inpsyde\MultilingualPress\Framework\Filter\Filter;
use Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;
use wpdb;

/**
 * Class ValidateRedirectFilter
 * @package Inpsyde\MultilingualPress\Module\QuickLinks
 */
class ValidateRedirectFilter implements Filter
{
    use FilterTrait;

    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * ValidateRedirectFilter constructor.
     * @param wpdb $wpdb
     */
    public function __construct(wpdb $wpdb)
    {
        $this->acceptedArgs = 0;
        $this->callback = [$this, 'enableExtendsAllowedHosts'];
        $this->hook = Redirector::ACTION_BEFORE_VALIDATE_REDIRECT;
        $this->priority = 10;
        $this->wpdb = $wpdb;
    }

    /**
     * Enable the filter
     *
     * @return void
     */
    public function enableExtendsAllowedHosts()
    {
        add_filter('allowed_redirect_hosts', [$this, 'extendsAllowedHosts'], 10, 2);
    }

    /**
     * Disable the filter
     *
     * @return bool
     */
    public function disable(): bool
    {
        return remove_filter(
            'allowed_redirect_hosts',
            [$this, 'extendsAllowedHosts'],
            10,
            2
        );
    }

    /**
     * Filter
     *
     * @param array $homeHosts
     * @param $remoteHosts
     * @return array
     */
    public function extendsAllowedHosts(array $homeHosts, string $remoteHosts): array
    {
        // Network with sub directories.
        if (\in_array($remoteHosts, $homeHosts, true)) {
            return $homeHosts;
        }

        $query = <<<SQL
SELECT domain
FROM %s
WHERE site_id = %d
	AND public   = "1"
	AND archived = "0"
	AND mature   = "0"
	AND spam     = "0"
	AND deleted  = "0"
ORDER BY domain DESC
SQL;

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        $domains = $this->wpdb->get_col(
            $this->wpdb->prepare($query, $this->wpdb->blogs, $this->wpdb->siteid)
        );
        //phpcs:enable

        if ($domains) {
            $allowedHosts = array_merge($homeHosts, $domains);
            $allowedHosts = array_unique($allowedHosts);
        }

        return $allowedHosts;
    }
}
