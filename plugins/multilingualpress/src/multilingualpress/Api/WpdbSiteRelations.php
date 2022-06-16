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

namespace Inpsyde\MultilingualPress\Api;

use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsOptions;
use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository;
use Inpsyde\MultilingualPress\Framework\Cache\Exception;
use Inpsyde\MultilingualPress\Database\Table\SiteRelationsTable;
use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Cache\Server\Facade;
use Inpsyde\MultilingualPress\Framework\Database\Table;

use function is_array;

/**
 * Site relations API implementation using the WordPress database object.
 */
final class WpdbSiteRelations implements SiteRelations
{
    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * @var Table
     */
    private $table;

    /**
     * @var Facade
     */
    private $cache;

    /**
     * @var CacheSettingsRepository
     */
    private $cacheSettingsRepository;

    /**
     * @param \wpdb $wpdb
     * @param Table $table
     * @param Facade $cache
     * @param CacheSettingsRepository $cacheSettingsRepository
     */
    public function __construct(
        \wpdb $wpdb,
        Table $table,
        Facade $cache,
        CacheSettingsRepository $cacheSettingsRepository
    ) {

        $this->wpdb = $wpdb;
        $this->table = $table;
        $this->cache = $cache;
        $this->cacheSettingsRepository = $cacheSettingsRepository;
    }

    /**
     * @inheritdoc
     */
    public function deleteRelation(int $sourceSite, int $targetSite = 0): int
    {
        if (!$this->table->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->table->name());
        }

        // Placeholders intended for \wpdb::prepare() have to be double-encoded for sprintf().
        if ($targetSite > 0) {
            $where = sprintf(
                '(%1$s = %%d AND %2$s = %%d) OR (%1$s = %%d AND %2$s = %%d)',
                SiteRelationsTable::COLUMN_SITE_1,
                SiteRelationsTable::COLUMN_SITE_2
            );

            //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
            //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            return (int)$this->wpdb->query(
                $this->wpdb->prepare(
                    "DELETE FROM {$this->table->name()} WHERE {$where}",
                    $sourceSite,
                    $targetSite,
                    $targetSite,
                    $sourceSite
                )
            );
            // phpcs:enable
        }

        $where = sprintf(
            '%1$s = %%d OR %2$s = %%d',
            SiteRelationsTable::COLUMN_SITE_1,
            SiteRelationsTable::COLUMN_SITE_2
        );

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return (int)$this->wpdb->query(
            $this->wpdb->prepare(
                "DELETE FROM {$this->table->name()} WHERE {$where}",
                $sourceSite,
                $sourceSite
            )
        );
        // phpcs:enable
    }

    /**
     * @inheritdoc
     */
    public function allRelations(): array
    {
        if (!$this->table->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->table->name());
        }

        $allowedCaching = $this->cacheSettingsRepository->get(
            CacheSettingsOptions::OPTION_GROUP_API_NAME,
            CacheSettingsOptions::OPTION_ALL_RELATIONS_API_NAME
        );

        if ($allowedCaching) {
            $cached = $this->allSiteRelationsCache();
            if ($cached) {
                return $cached;
            }
        }

        $query = sprintf(
            'SELECT %2$s, %3$s FROM %1$s ORDER BY %2$s ASC, %3$s ASC',
            $this->table->name(),
            SiteRelationsTable::COLUMN_SITE_1,
            SiteRelationsTable::COLUMN_SITE_2
        );

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        $rows = $this->wpdb->get_results($query, ARRAY_A);
        // phpcs:enable

        return $rows
            ? $this->siteRelationsFromQueryResults($rows)
            : [];
    }

    /**
     * @inheritdoc
     */
    public function relatedSiteIds(int $siteId, bool $includeSite = false): array
    {
        if (!$this->table->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->table->name());
        }

        if (!absint($siteId)) {
            return [];
        }

        $allowedCaching = $this->cacheSettingsRepository->get(
            CacheSettingsOptions::OPTION_GROUP_API_NAME,
            CacheSettingsOptions::OPTION_RELATED_SITE_IDS_API_NAME
        );

        if ($allowedCaching) {
            $cached = $this->cache->claim(self::RELATED_SITE_IDS_CACHE_KEY, $siteId, $includeSite);
            if ($cached && is_array($cached)) {
                return $cached;
            }
        }

        // Placeholders intended for \wpdb::prepare() have to be double-encoded for sprintf().
        $queryFormat = <<<'SQL'
(SELECT DISTINCT %2$s AS site_id FROM %1$s WHERE %3$s = %%d)
UNION (SELECT DISTINCT %3$s FROM %1$s WHERE %2$s = %%d)
ORDER BY site_id ASC
SQL;

        $query = sprintf(
            $queryFormat,
            $this->table->name(),
            SiteRelationsTable::COLUMN_SITE_1,
            SiteRelationsTable::COLUMN_SITE_2
        );

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        $rows = $this->wpdb->get_col(
            $this->wpdb->prepare(
                $query,
                $siteId,
                $siteId
            )
        );
        // phpcs:enable

        if (!$rows) {
            return [];
        }

        if ($includeSite) {
            $rows[] = $siteId;
        }

        return array_map('intval', $rows);
    }

    /**
     * @inheritdoc
     */
    public function insertRelations(int $baseSiteId, array $siteIds): int
    {
        if (!$this->table->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->table->name());
        }

        // We don't want to relate a site with itself.
        $siteIds = array_diff($siteIds, [$baseSiteId]);
        if (!$siteIds) {
            return 0;
        }

        $values = '';
        foreach ($siteIds as $siteId) {
            $values and $values .= ',';
            $values .= $this->valuePair($baseSiteId, (int)$siteId);
        }

        $query = sprintf(
            'INSERT IGNORE INTO %1$s (%2$s, %3$s) VALUES %4$s',
            $this->table->name(),
            SiteRelationsTable::COLUMN_SITE_1,
            SiteRelationsTable::COLUMN_SITE_2,
            $values
        );

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        return (int)$this->wpdb->query($query);
        // phpcs:enable
    }

    /**
     * @inheritdoc
     */
    public function relateSites(int $baseSiteId, array $siteIds): int
    {
        $relatedSiteIds = $this->relatedSiteIds($baseSiteId);
        if ($relatedSiteIds === $siteIds) {
            return 0;
        }

        $toDelete = array_diff($relatedSiteIds, $siteIds);
        $changed = 0;
        foreach ($toDelete as $siteId) {
            $changed += $this->deleteRelation($baseSiteId, $siteId);
        }

        $toInsert = $toDelete ? array_diff($siteIds, $toDelete) : $siteIds;

        return $changed + $this->insertRelations($baseSiteId, $toInsert);
    }

    /**
     * Returns a (value1, value2) syntax string according to the given site IDs.
     *
     * @param int $site1
     * @param int $site2
     * @return string
     */
    private function valuePair(int $site1, int $site2): string
    {
        // Swap values to make sure the lower value is the first.
        if ($site1 > $site2) {
            list($site1, $site2) = [$site2, $site1];
        }

        return "($site1, $site2)";
    }

    /**
     * Returns a formatted array with site relations included in the given query results.
     *
     * @param string[] $rows
     * @return int[][]
     */
    private function siteRelationsFromQueryResults(array $rows): array
    {
        $relations = array_reduce(
            $rows,
            static function (array $relations, array $row): array {
                $site1 = (int)$row[SiteRelationsTable::COLUMN_SITE_1];
                $site2 = (int)$row[SiteRelationsTable::COLUMN_SITE_2];
                $relations[$site1][$site2] = $site2;
                $relations[$site2][$site1] = $site1;

                return $relations;
            },
            []
        );

        return array_map('array_values', $relations);
    }

    /**
     * @return array
     */
    private function allSiteRelationsCache(): array
    {
        try {
            $cached = $this->cache->claim(self::ALL_RELATIONS_CACHE_KEY);
            $cached = is_array($cached) ? $cached : [];
        } catch (Exception\NotRegisteredCacheItem $exc) {
            $cached = [];
        }

        return $cached;
    }
}
