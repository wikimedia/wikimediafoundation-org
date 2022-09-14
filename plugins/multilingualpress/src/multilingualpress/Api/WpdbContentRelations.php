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
use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository;
use Inpsyde\MultilingualPress\Database\Table\RelationshipsTable;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Cache\Server\Facade;
use Inpsyde\MultilingualPress\Framework\NetworkState;
use Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes;
use Inpsyde\MultilingualPress\Core\Entity\ActiveTaxonomies;
use Inpsyde\MultilingualPress\Database\Table\ContentRelationsTable;

use function Inpsyde\MultilingualPress\siteExists;
use function is_array;

/**
 * Content relations API implementation using the WordPress database object.
 */
final class WpdbContentRelations implements ContentRelations
{
    /**
     * @var ActivePostTypes
     */
    private $activePostTypes;

    /**
     * @var ActiveTaxonomies
     */
    private $activeTaxonomies;

    /**
     * @var \wpdb
     */
    private $wpdb;

    /**
     * @var RelationshipsTable
     */
    private $relationshipsTable;

    /**
     * @var ContentRelationsTable
     */
    private $contentRelationshipTable;

    /**
     * @var Facade
     */
    private $cache;

    /**
     * @var CacheSettingsRepository
     */
    private $cacheSettingsRepository;

    /**
     * @var SiteSettingsRepository
     */
    private $siteSettingsRepository;

    /**
     * @var SiteRelations
     */
    private $siteRelations;

    /**
     * @param \wpdb $wpdb
     * @param ContentRelationsTable $contentRelationshipTable
     * @param RelationshipsTable $relationshipsTable
     * @param ActivePostTypes $activePostTypes
     * @param ActiveTaxonomies $activeTaxonomies
     * @param Facade $cache
     * @param CacheSettingsRepository $cacheSettingsRepository
     * @param SiteSettingsRepository $siteSettingsRepository
     * @param SiteRelations $siteRelations
     */
    public function __construct(
        \wpdb $wpdb,
        ContentRelationsTable $contentRelationshipTable,
        RelationshipsTable $relationshipsTable,
        ActivePostTypes $activePostTypes,
        ActiveTaxonomies $activeTaxonomies,
        Facade $cache,
        CacheSettingsRepository $cacheSettingsRepository,
        SiteSettingsRepository $siteSettingsRepository,
        SiteRelations $siteRelations
    ) {

        $this->wpdb = $wpdb;
        $this->contentRelationshipTable = $contentRelationshipTable;
        $this->relationshipsTable = $relationshipsTable;
        $this->activePostTypes = $activePostTypes;
        $this->activeTaxonomies = $activeTaxonomies;
        $this->cache = $cache;
        $this->cacheSettingsRepository = $cacheSettingsRepository;
        $this->siteSettingsRepository = $siteSettingsRepository;
        $this->siteRelations = $siteRelations;
    }

    /**
     * @inheritdoc
     */
    public function createRelationship(array $contentIds, string $type): int
    {
        $relationshipId = $this->createRelationshipForType($type);
        if (!$relationshipId) {
            return $relationshipId;
        }

        foreach ($contentIds as $siteId => $contentId) {
            $this->saveRelation($relationshipId, (int)$siteId, (int)$contentId);
        }

        return $relationshipId;
    }

    /**
     * @inheritdoc
     */
    public function deleteAllRelationsForInvalidContent(string $type): bool
    {
        if (!$this->contentRelationshipTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->contentRelationshipTable->name());
        }

        $relationshipIds = $this->relationshipIdsByType($type);
        if (!$relationshipIds) {
            return true;
        }

        $relationshipIds = implode(',', $relationshipIds);
        $networkState = NetworkState::create();
        $siteIds = (array)get_sites(['fields' => 'ids']);

        foreach ($siteIds as $siteId) {
            switch_to_blog($siteId);

            $queryTemplate = sprintf(
                'SELECT %2$s FROM %1$s WHERE %3$s = %%d AND %2$s NOT IN (%5$s) AND %4$s IN (%%s)',
                $this->contentRelationshipTable->name(),
                ContentRelationsTable::COLUMN_CONTENT_ID,
                ContentRelationsTable::COLUMN_SITE_ID,
                ContentRelationsTable::COLUMN_RELATIONSHIP_ID,
                implode(',', $this->existingContentIds($type))
            );

            //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
            $query = $this->wpdb->prepare(
                $queryTemplate,
                [
                    $siteId,
                    $relationshipIds,
                ]
            );

            $contentIds = $this->wpdb->get_col($query);
            foreach ($contentIds as $contentId) {
                $this->deleteRelation(
                    [$siteId => (int)$contentId],
                    $type
                );
            }
            // phpcs:enable
        }

        $networkState->restore();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteAllRelationsForInvalidSites(): bool
    {
        if (!$this->contentRelationshipTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->contentRelationshipTable->name());
        }

        $query = sprintf(
            'SELECT DISTINCT %2$s FROM %1$s WHERE %2$s NOT IN (SELECT blog_id FROM %3$s)',
            $this->contentRelationshipTable->name(),
            ContentRelationsTable::COLUMN_SITE_ID,
            $this->wpdb->blogs
        );

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        $siteIds = $this->wpdb->get_col($query);
        // phpcs:enable
        $settings = $this->siteSettingsRepository->allSettings();
        foreach ($settings as $siteId => $setting) {
            if (siteExists($siteId)) {
                continue;
            }

            $this->siteRelations->deleteRelation($siteId);
            $siteIds[] = $siteId;
            unset($settings[$siteId]);
            $this->siteSettingsRepository->updateSettings($settings);
        }

        $errors = 0;
        foreach ($siteIds as $siteId) {
            if (!$this->deleteAllRelationsForSite((int)$siteId)) {
                $errors++;
            }
        }

        return $errors === 0;
    }

    /**
     * @inheritdoc
     */
    public function deleteAllRelationsForSite(int $siteId): bool
    {
        $relationshipIds = $this->relationshipIdsBySiteId($siteId);

        $errors = 0;
        foreach ($relationshipIds as $relationshipId) {
            if (!$this->deleteRelationForSite($relationshipId, $siteId)) {
                $errors++;
            }
        }

        return $errors === 0;
    }

    /**
     * @inheritdoc
     */
    public function deleteRelation(array $contentIds, string $type): bool
    {
        $relationshipId = $this->relationshipId($contentIds, $type);

        if (!$relationshipId) {
            return true;
        }

        $siteIds = array_map('intval', array_keys($contentIds));
        $errors = 0;

        foreach ($siteIds as $siteId) {
            if (!$this->deleteRelationForSite($relationshipId, $siteId)) {
                $errors++;
            }
        }

        return $errors === 0;
    }

    /**
     * @inheritdoc
     */
    public function duplicateRelations(int $sourceSiteId, int $targetSiteId): int
    {
        if (!$this->contentRelationshipTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->contentRelationshipTable->name());
        }

        // Placeholders intended for \wpdb::prepare() have to be double-encoded for sprintf().
        $query = sprintf(
            'INSERT INTO %1$s SELECT %2$s, %%d, %3$s FROM %1$s WHERE %4$s = %%d',
            $this->contentRelationshipTable->name(),
            ContentRelationsTable::COLUMN_RELATIONSHIP_ID,
            ContentRelationsTable::COLUMN_CONTENT_ID,
            ContentRelationsTable::COLUMN_SITE_ID
        );

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        $query = $this->wpdb->prepare(
            $query,
            $targetSiteId,
            $sourceSiteId
        );

        return (int)$this->wpdb->query($query);
        // phpcs:enable
    }

    /**
     * @inheritdoc
     */
    public function contentId(int $relationshipId, int $siteId): int
    {
        return $this->contentIds($relationshipId)[$siteId] ?? 0;
    }

    /**
     * @inheritdoc
     */
    public function contentIdForSite(
        int $siteId,
        int $contentId,
        string $type,
        int $targetSiteId
    ): int {

        $relations = $this->relations($siteId, $contentId, $type);

        return $relations[$targetSiteId] ?? 0;
    }

    /**
     * @inheritdoc
     */
    public function contentIds(int $relationshipId): array
    {
        if (!$this->contentRelationshipTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->contentRelationshipTable->name());
        }

        $allowedCaching = $this->cacheSettingsRepository->get(
            CacheSettingsOptions::OPTION_GROUP_API_NAME,
            CacheSettingsOptions::OPTION_CONTENT_IDS_API_NAME
        );
        if ($allowedCaching) {
            $cached = $this->cache->claim(self::CONTENT_IDS_CACHE_KEY, $relationshipId);
            if ($cached && is_array($cached)) {
                return $cached;
            }
        }

        //  Placeholders intended for \wpdb::prepare() have to be double-encoded for sprintf().
        $query = sprintf(
            'SELECT %2$s, %3$s FROM %1$s WHERE %4$s = %%d',
            $this->contentRelationshipTable->name(),
            ContentRelationsTable::COLUMN_SITE_ID,
            ContentRelationsTable::COLUMN_CONTENT_ID,
            ContentRelationsTable::COLUMN_RELATIONSHIP_ID
        );

        /** @var array[] $rows */
        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare($query, $relationshipId),
            ARRAY_A
        );
        // phpcs:enable

        $contentIds = [];
        foreach ($rows as $row) {
            $value = (int)$row[ContentRelationsTable::COLUMN_CONTENT_ID];
            $key = (int)$row[ContentRelationsTable::COLUMN_SITE_ID];
            $contentIds[$key] = $value;
        }

        return $contentIds;
    }

    /**
     * @inheritdoc
     */
    public function relations(int $siteId, int $contentId, string $type): array
    {
        $allowedCaching = $this->cacheSettingsRepository->get(
            CacheSettingsOptions::OPTION_GROUP_API_NAME,
            CacheSettingsOptions::OPTION_RELATIONS_API_NAME
        );

        if ($allowedCaching) {
            $cached = $this->cache->claim(self::RELATIONS_CACHE_KEY, $siteId, $contentId, $type);
            if ($cached && is_array($cached)) {
                return $cached;
            }
        }

        $relationshipId = $this->singleRelationshipIdFor($siteId, $contentId, $type);

        return $relationshipId ? $this->contentIds($relationshipId) : [];
    }

    /**
     * @inheritdoc
     */
    public function relationshipId(array $contentIds, string $type, bool $create = false): int
    {
        $contentIds = array_filter($contentIds);
        if (!$contentIds) {
            // Error: No contents given!
            return 0;
        }

        $relationshipId = $this->multipleRelationshipIdFor($contentIds, $type);

        if (!$relationshipId && $create) {
            return $this->createRelationshipForType($type);
        }

        return $relationshipId;
    }

    /**
     * @inheritdoc
     */
    public function hasSiteRelations(int $siteId, string $type = ''): bool
    {
        if (!$this->contentRelationshipTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->contentRelationshipTable->name());
        }

        $allowedCaching = $this->cacheSettingsRepository->get(
            CacheSettingsOptions::OPTION_GROUP_API_NAME,
            CacheSettingsOptions::OPTION_HAS_SITE_RELATIONS_API_NAME
        );

        if ($allowedCaching) {
            $cached = $this->cache->claim(self::HAS_SITE_RELATIONS_CACHE_KEY, $siteId, $type);
            if ($cached !== null) {
                return (bool)$cached;
            }
        }

        if ($type) {
            return $this->hasSiteRelationsOfType($siteId, $type);
        }

        //  Placeholders intended for \wpdb::prepare() have to be double-encoded for sprintf().
        $query = sprintf(
            'SELECT %2$s FROM %1$s WHERE %3$s = %%d LIMIT 1',
            $this->contentRelationshipTable->name(),
            ContentRelationsTable::COLUMN_CONTENT_ID,
            ContentRelationsTable::COLUMN_SITE_ID
        );

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        return (bool)$this->wpdb->query($this->wpdb->prepare($query, $siteId));
        // phpcs:enable
    }

    /**
     * @inheritdoc
     */
    public function relateAllPosts(int $sourceSite, int $targetSite): bool
    {
        $postIds = $this->postIdsToRelate();

        $errors = 0;
        foreach ($postIds as $postId) {
            $relId = $this->relationshipId(
                [$sourceSite => $postId, $targetSite => $postId],
                ContentRelations::CONTENT_TYPE_POST,
                true
            );

            $this->saveRelation($relId, $sourceSite, $postId) or $errors++;
            $this->saveRelation($relId, $targetSite, $postId) or $errors++;
        }

        return $errors === 0;
    }

    /**
     * @inheritdoc
     */
    public function relateAllTerms(int $sourceSite, int $targetSite): bool
    {
        $termTaxonomyIds = $this->termTaxonomyIdsToRelate();

        $errors = 0;
        foreach ($termTaxonomyIds as $ttId) {
            $relId = $this->relationshipId(
                [$sourceSite => $ttId, $targetSite => $ttId],
                ContentRelations::CONTENT_TYPE_TERM,
                true
            );

            $this->saveRelation($relId, $sourceSite, $ttId) or $errors++;
            $this->saveRelation($relId, $targetSite, $ttId) or $errors++;
        }

        return $errors === 0;
    }

    /**
     * @inheritdoc
     */
    public function saveRelation(int $relationshipId, int $siteId, int $contentId): bool
    {
        if (0 === $contentId) {
            return $this->deleteRelationForSite($relationshipId, $siteId);
        }

        $currentContentId = $this->contentId($relationshipId, $siteId);

        if ($currentContentId && $currentContentId === $contentId) {
            return true;
        }

        if ($currentContentId) {
            // Delete different relation of the given site.
            $this->deleteRelationForSite($relationshipId, $siteId, false);
        }

        $type = $this->relationshipType($relationshipId);
        if (!$type) {
            return $this->insertRelation($relationshipId, $siteId, $contentId);
        }

        $currentRelId = $this->singleRelationshipIdFor($siteId, $contentId, $type);
        if ($currentRelId && $currentRelId !== $relationshipId) {
            // Delete different relation of the given content element.
            $this->deleteRelationForSite($currentRelId, $siteId);
        }

        return $this->insertRelation($relationshipId, $siteId, $contentId);
    }

    /**
     * Creates a new relationship for the given type.
     *
     * @param string $type
     * @return int
     * @throws NonexistentTable
     */
    private function createRelationshipForType(string $type): int
    {
        if (!$this->relationshipsTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->relationshipsTable->name());
        }

        $dbInsert = $this->wpdb->insert(
            $this->relationshipsTable->name(),
            [RelationshipsTable::COLUMN_TYPE => $type],
            '%s'
        );

        return $dbInsert ? (int)$this->wpdb->insert_id : 0;
    }

    /**
     * Deletes the relation for the given arguments.
     *
     * @param int $relationshipId
     * @param int $siteId
     * @param bool $delete
     * @return bool
     * @throws NonexistentTable
     */
    private function deleteRelationForSite(
        int $relationshipId,
        int $siteId,
        bool $delete = true
    ): bool {

        if (!$this->contentRelationshipTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->contentRelationshipTable->name());
        }

        $contentIds = $this->contentIds($relationshipId);

        if (count($contentIds) < 3 && $delete && !empty($contentIds[$siteId])) {
            return $this->deleteRelationship($relationshipId);
        }

        $dbDelete = $this->wpdb->delete(
            $this->contentRelationshipTable->name(),
            [
                ContentRelationsTable::COLUMN_RELATIONSHIP_ID => $relationshipId,
                ContentRelationsTable::COLUMN_SITE_ID => $siteId,
            ],
            '%d'
        );

        return $dbDelete !== false;
    }

    /**
     * Removes the relationship as well as all relations with the given relationship ID.
     *
     * @param int $relationshipId
     * @return bool
     * @throws NonexistentTable
     */
    private function deleteRelationship(int $relationshipId): bool
    {
        if (!$this->relationshipsTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->relationshipsTable->name());
        }
        if (!$this->contentRelationshipTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->contentRelationshipTable->name());
        }

        $deleteFromRelTable = $this->wpdb->delete(
            $this->relationshipsTable->name(),
            [RelationshipsTable::COLUMN_ID => $relationshipId],
            '%d'
        );

        if ($deleteFromRelTable === false) {
            return false;
        }

        $delete = $this->wpdb->delete(
            $this->contentRelationshipTable->name(),
            [ContentRelationsTable::COLUMN_RELATIONSHIP_ID => $relationshipId],
            '%d'
        );

        return $delete !== false;
    }

    /**
     * Returns the IDs of all existing content elements of the given type in the current site.
     *
     * @param string $type
     * @return int[]
     */
    private function existingContentIds(string $type): array
    {
        $queries = [
            self::CONTENT_TYPE_POST => "SELECT ID FROM {$this->wpdb->posts}",
            self::CONTENT_TYPE_TERM => "SELECT term_taxonomy_id FROM {$this->wpdb->term_taxonomy}",
        ];

        if (!array_key_exists($type, $queries)) {
            return [];
        }

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        return array_map('intval', $this->wpdb->get_col($queries[$type]));
        // phpcs:enable
    }

    /**
     * Returns the IDs of the posts to relate for the current site.
     *
     * @return int[]
     */
    private function postIdsToRelate(): array
    {
        $query = "SELECT ID FROM {$this->wpdb->posts} WHERE 1 = 1";

        /**
         * Filters the post status to be used for post relations.
         *
         * @param string[] $statuses
         */
        $statuses = (array)apply_filters(
            ContentRelations::FILTER_POST_STATUS,
            [
                'draft',
                'future',
                'pending',
                'private',
                'publish',
            ]
        );

        $statuses = array_filter($statuses);
        if ($statuses) {
            $statusesString = implode("','", $statuses);
            $query .= " AND post_status IN ('{$statusesString}')";
        }

        $types = (array)apply_filters(
            ContentRelations::FILTER_POST_TYPE,
            $this->activePostTypes->names()
        );

        $types = array_filter($types);
        if ($types) {
            $typesString = implode("','", $types);
            $query .= " AND post_type IN ('{$typesString}')";
        }

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        return array_map('intval', $this->wpdb->get_col($query));
        // phpcs:enable
    }

    /**
     * Returns the relationship ID for the given arguments.
     *
     * @param int[] $contentIds
     * @param string $type
     * @return int
     */
    private function multipleRelationshipIdFor(array $contentIds, string $type): int
    {
        $relationshipId = 0;

        foreach ($contentIds as $siteId => $contentId) {
            $newRelationshipId = $this->singleRelationshipIdFor(
                (int)$siteId,
                (int)$contentId,
                $type
            );

            if (!$newRelationshipId) {
                continue;
            }

            if (!$relationshipId) {
                $relationshipId = $newRelationshipId;
            } elseif ($relationshipId !== $newRelationshipId) {
                // Error: Different relationship IDs!
                return 0;
            }
        }

        return $relationshipId;
    }

    /**
     * Returns the relationship ID for the given arguments.
     *
     * @param int $siteId
     * @param int $contentId
     * @param string $type
     * @return int
     * @throws NonexistentTable
     */
    private function singleRelationshipIdFor(int $siteId, int $contentId, string $type): int
    {
        if (!$this->relationshipsTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->relationshipsTable->name());
        }
        if (!$this->contentRelationshipTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->contentRelationshipTable->name());
        }

        if ($siteId < 1 || $contentId < 1 || !$type) {
            return 0;
        }

        $colRelId = RelationshipsTable::COLUMN_ID;
        $colRelType = RelationshipsTable::COLUMN_TYPE;
        $colContentRelId = ContentRelationsTable::COLUMN_RELATIONSHIP_ID;
        $colContentSiteId = ContentRelationsTable::COLUMN_SITE_ID;
        $colContentContentId = ContentRelationsTable::COLUMN_CONTENT_ID;
        $contentRelationshipTableName = $this->contentRelationshipTable->name();
        $relationshipsTableName = $this->relationshipsTable->name();

        $sql = <<<SQL
SELECT r.$colRelId FROM $relationshipsTableName r
INNER JOIN $contentRelationshipTableName t ON r.$colRelId = t.$colContentRelId
WHERE t.$colContentSiteId = %d
  AND t.$colContentContentId = %d
  AND r.$colRelType = %s
SQL;

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        return (int)$this->wpdb->get_var($this->wpdb->prepare($sql, $siteId, $contentId, $type));
        // phpcs:enable
    }

    /**
     * Returns the relationship IDs for the site with the given ID.
     *
     * @param int $siteId
     * @return int[]
     * @throws NonexistentTable
     */
    private function relationshipIdsBySiteId(int $siteId): array
    {
        if (!$this->contentRelationshipTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->contentRelationshipTable->name());
        }

        if ($siteId < 1) {
            return [];
        }

        $colRelId = ContentRelationsTable::COLUMN_RELATIONSHIP_ID;
        $colSiteId = ContentRelationsTable::COLUMN_SITE_ID;

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $relIds = $this->wpdb->get_col(
            $this->wpdb->prepare(
                "SELECT {$colRelId} FROM {$this->contentRelationshipTable->name()} WHERE {$colSiteId} = %d",
                $siteId
            )
        );
        // phpcs:enable

        return wp_parse_id_list($relIds);
    }

    /**
     * Returns the relationship IDs for the given type.
     *
     * @param string $type
     * @return int[]
     * @throws NonexistentTable
     */
    private function relationshipIdsByType(string $type): array
    {
        if (!$this->relationshipsTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->relationshipsTable->name());
        }

        if (!$type) {
            return [];
        }

        $colId = RelationshipsTable::COLUMN_ID;
        $colType = RelationshipsTable::COLUMN_TYPE;
        $query = "SELECT {$colId} FROM {$this->relationshipsTable->name()} WHERE {$colType} = %s";

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        return wp_parse_id_list($this->wpdb->get_col($this->wpdb->prepare($query, $type)));
        // phpcs:enable
    }

    /**
     * Return the content type for the relationship with the given ID.
     *
     * @param int $relationshipId
     * @return string
     * @throws NonexistentTable
     */
    private function relationshipType(int $relationshipId): string
    {
        if (!$this->relationshipsTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->relationshipsTable->name());
        }

        if ($relationshipId < 1) {
            return '';
        }

        $colType = RelationshipsTable::COLUMN_TYPE;
        $colId = RelationshipsTable::COLUMN_ID;

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return (string)$this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT {$colType} FROM {$this->relationshipsTable->name()} WHERE {$colId} = %d",
                $relationshipId
            )
        );
        // phpcs:enable
    }

    /**
     * Returns the IDs of the terms to relate for the current site.
     *
     * @return int[]
     */
    private function termTaxonomyIdsToRelate(): array
    {
        $query = "SELECT term_taxonomy_id FROM {$this->wpdb->term_taxonomy} WHERE 1 = 1";

        /**
         * Filters the taxonomy to be used for term relations.
         *
         * @param string[] $taxonomies
         */
        $taxonomies = (array)apply_filters(
            ContentRelations::FILTER_TAXONOMY,
            $this->activeTaxonomies->names()
        );

        $taxonomies = array_filter($taxonomies);

        if ($taxonomies) {
            $taxonomiesString = implode("','", $taxonomies);
            $query .= " AND taxonomy IN ('{$taxonomiesString}')";
        }

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        return array_map('intval', $this->wpdb->get_col($query));
    }

    /**
     * Checks if the site with the given ID has any relations of the given content type.
     *
     * @param int $siteId
     * @param string $type
     * @return bool
     * @throws NonexistentTable
     */
    private function hasSiteRelationsOfType(int $siteId, string $type): bool
    {
        if (!$this->relationshipsTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->relationshipsTable->name());
        }
        if (!$this->contentRelationshipTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->contentRelationshipTable->name());
        }

        // Placeholders intended for \wpdb::prepare() have to be double-encoded for sprintf().
        $sqlFormat = 'SELECT t.%2$s FROM %1$s t JOIN %3$s r ON t.%4$s = r.%5$s ';
        $sqlFormat .= 'WHERE t.%6$s = %%d AND r.%7$s = %%s LIMIT 1';

        $query = sprintf(
            $sqlFormat,
            $this->contentRelationshipTable->name(),
            ContentRelationsTable::COLUMN_CONTENT_ID,
            $this->relationshipsTable->name(),
            ContentRelationsTable::COLUMN_RELATIONSHIP_ID,
            RelationshipsTable::COLUMN_ID,
            ContentRelationsTable::COLUMN_SITE_ID,
            RelationshipsTable::COLUMN_TYPE
        );

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        return (bool)$this->wpdb->query($this->wpdb->prepare($query, $siteId, $type));
    }

    /**
     * Inserts a new relation with the given values.
     *
     * @param int $relationshipId
     * @param int $siteId
     * @param int $contentId
     * @return bool
     * @throws NonexistentTable
     */
    private function insertRelation(
        int $relationshipId,
        int $siteId,
        int $contentId
    ): bool {

        if (!$this->contentRelationshipTable->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->contentRelationshipTable->name());
        }

        return (bool)$this->wpdb->insert(
            $this->contentRelationshipTable->name(),
            [
                ContentRelationsTable::COLUMN_RELATIONSHIP_ID => $relationshipId,
                ContentRelationsTable::COLUMN_SITE_ID => $siteId,
                ContentRelationsTable::COLUMN_CONTENT_ID => $contentId,
            ],
            '%d'
        );
    }
}
