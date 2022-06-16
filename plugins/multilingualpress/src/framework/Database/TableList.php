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

namespace Inpsyde\MultilingualPress\Framework\Database;

use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions;
use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository;
use Inpsyde\MultilingualPress\Framework\Cache\Exception;
use Inpsyde\MultilingualPress\Framework\Cache\Server\Facade;
use Throwable;

/**
 * Table list implementation using the WordPress database object.
 */
class TableList
{
    const ALL_TABLES_CACHE_KEY = 'allTables';

    /**
     * @var \wpdb
     */
    private $db;

    /**
     * @var Facade
     */
    private $cache;

    /**
     * @var CacheSettingsRepository
     */
    private $cacheSettingsRepository;

    /**
     * @param \wpdb $db
     * @param Facade $cache
     * @param CacheSettingsRepository $cacheSettingsRepository
     */
    public function __construct(
        \wpdb $db,
        Facade $cache,
        CacheSettingsRepository $cacheSettingsRepository
    ) {

        $this->db = $db;
        $this->cache = $cache;
        $this->cacheSettingsRepository = $cacheSettingsRepository;

        /**
         * WordPress file with the wp_get_db_schema() function.
         */
        require_once ABSPATH . 'wp-admin/includes/schema.php';
    }

    /**
     * Returns an array with the names of all tables for the site with the given ID.
     * By default will return main site tables.
     *
     * @param int|null $siteId
     * @return array of all table names for given site
     * @throws Throwable
     */
    public function allTablesForSite(int $siteId = null): array
    {
        /*
         * This method used to return global tables when the given site was the main site ID,
         * and that was kind of a bug and required `siteTables()` to make use of this method but
         * doing an array diff against network tables.
         * Now that bug is fixed and the login is implemented in `siteTables()`, which is mostly
         * used in the rest of MLP.
         * At this point, this method is pretty much useless, and could be probably deprecated,
         * for now we keep it as a way to default site ID to current site, whereas `siteTables()`
         * always require site ID parameter.
         */

        return $this->siteTables($siteId ?? get_main_site_id());
    }

    /**
     * Returns an array with the names of all tables.
     *
     * @return array of all table names for given site
     * @throws Throwable
     */
    public function allTables(): array
    {
        $allowedCaching = $this->cacheSettingsRepository->get(
            CacheSettingsOptions::OPTION_GROUP_DATABASE_NAME,
            CacheSettingsOptions::OPTION_ALL_TABLES_DATABASE_NAME
        );
        if ($allowedCaching) {
            $cached = $this->allTablesCache();
            if ($cached) {
                return $cached;
            }
        }

        $query = $this->db->prepare(
            "SHOW TABLES LIKE '%s'",
            "{$this->db->base_prefix}%"
        );

        return (array)$this->db->get_col($query);
    }

    /**
     * Returns an array with the names of all network tables.
     *
     * @return array The array of network table names
     * @throws Throwable
     */
    public function networkTables(): array
    {
        return array_intersect($this->allTables(), $this->db->tables('global'));
    }

    /**
     * Returns an array with the names of all tables for the site with the given ID.
     *
     * @param int $siteId
     * @return array The array of site table names
     * @throws Throwable
     */
    public function siteTables(int $siteId): array
    {
        global $wpdb;
        if ($siteId !== (int)get_main_site_id()) {
            $prefix = str_replace("_", "\_", $wpdb->prefix);
            //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            return $wpdb->get_col("SHOW TABLES LIKE '{$prefix}%'");
        }
        // phpcs:enable

        $allTables = $this->allTables();
        if (!$allTables) {
            return [];
        }

        $globalTables = $this->db->tables('global');

        /*
         * For the main site, we would need to use a REGEXP query to get site-specific tables.
         * That requires we know the column name to apply the REGEXP expression to, and we could
         * construct it starting from the db name, something like `"Tables_in_{$wpdb->dbname}"`.
         * However, it seems `$wpdb->dbname` is empty in some environments (like wp.com VIP), at
         * least in some circumstances.
         * For that reason, we loop all table names, skipping global tables and any table name that
         * has a numeric character right after the WPDB base prefix.
         */
        $prefixLen = strlen($wpdb->base_prefix);
        $mainSiteTables = [];
        foreach ($allTables as $table) {
            $isGlobal = in_array($table, $globalTables, true);
            if (!$isGlobal && ((int)substr($table, $prefixLen, 1) === 0)) {
                $mainSiteTables[] = $table;
            }
        }

        return $mainSiteTables;
    }

    /**
     * @return array
     * @throws Exception\InvalidCacheArgument
     * @throws Exception\InvalidCacheDriver
     */
    private function allTablesCache(): array
    {
        try {
            $cached = $this->cache->claim(self::ALL_TABLES_CACHE_KEY);
            $cached = is_array($cached) ? $cached : [];
        } catch (Exception\NotRegisteredCacheItem $exc) {
            $cached = [];
        }

        return $cached;
    }
}
