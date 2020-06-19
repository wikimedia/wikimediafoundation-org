<?php # -*- coding: utf-8 -*-
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
     * Returns an array with the names of all tables.
     *
     * @return string[]
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
     * @return string[]
     */
    public function networkTables(): array
    {
        $networkTables = $this->extractTablesFromSchema(
            wp_get_db_schema('global'),
            $this->db->base_prefix
        );

        return array_intersect($this->allTables(), $networkTables);
    }

    /**
     * Returns an array with the names of all tables for the site with the given ID.
     *
     * @param int $siteId
     * @return string[]
     */
    public function siteTables(int $siteId): array
    {
        return $this->extractTablesFromSchema(
            wp_get_db_schema('blog', $siteId),
            $this->db->get_blog_prefix($siteId)
        );
    }

    /**
     * Extracts all table names (including the given prefix) from the given schema.
     *
     * @param string $schema
     * @param string $prefix
     * @return string[]
     */
    private function extractTablesFromSchema(string $schema, string $prefix = ''): array
    {
        preg_match_all("~CREATE TABLE ({$prefix}.*) \(~", $schema, $matches);

        return empty($matches[1]) ? [] : $matches[1];
    }

    /**
     * @return array
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
