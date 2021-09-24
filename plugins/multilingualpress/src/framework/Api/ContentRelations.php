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

namespace Inpsyde\MultilingualPress\Framework\Api;

use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;

/**
 * Interface for all content relations API implementations.
 */
interface ContentRelations
{
    const CONTENT_IDS_CACHE_KEY = 'contentIds';
    const RELATIONS_CACHE_KEY = 'relations';
    const HAS_SITE_RELATIONS_CACHE_KEY = 'hasSiteRelations';

    const CONTENT_TYPE_POST = 'post';
    const CONTENT_TYPE_TERM = 'term';
    const FILTER_POST_TYPE = 'multilingualpress.content_relations_post_type';
    const FILTER_POST_STATUS = 'multilingualpress.content_relations_post_status';
    const FILTER_TAXONOMY = 'multilingualpress.content_relations_taxonomy';

    /**
     * Creates a relationship for the given content ids provided as an array with site IDs as keys
     * and content IDs as values.
     *
     * @param int[] $contentIds
     * @param string $type
     * @return int
     * @throws NonexistentTable
     */
    public function createRelationship(array $contentIds, string $type): int;

    /**
     * Deletes all relations for content elements that don't exist (anymore).
     *
     * @param string $type
     * @return bool
     * @throws NonexistentTable
     */
    public function deleteAllRelationsForInvalidContent(string $type): bool;

    /**
     * Deletes all relations for sites that don't exist (anymore).
     *
     * @return bool
     * @throws NonexistentTable
     */
    public function deleteAllRelationsForInvalidSites(): bool;

    /**
     * Deletes all relations for the site with the given ID.
     *
     * @param int $siteId
     * @return bool
     * @throws NonexistentTable
     */
    public function deleteAllRelationsForSite(int $siteId): bool;

    /**
     * Deletes a relation according to the given arguments.
     *
     * @param int[] $contentIds
     * @param string $type
     * @return bool
     * @throws NonexistentTable
     */
    public function deleteRelation(array $contentIds, string $type): bool;

    /**
     * Copies all relations of the given (or any) content type from the given source site to the
     * given destination site.
     *
     * This method is suited to be used after site duplication, because both sites are assumed to
     * have the exact same content IDs.
     *
     * @param int $sourceSiteId
     * @param int $targetSiteId
     * @return int
     * @throws NonexistentTable
     */
    public function duplicateRelations(int $sourceSiteId, int $targetSiteId): int;

    /**
     * Returns the content ID for the given arguments.
     *
     * @param int $relationshipId
     * @param int $siteId
     * @return int
     * @throws NonexistentTable
     */
    public function contentId(int $relationshipId, int $siteId): int;

    /**
     * Returns the content ID in the given target site for the given content element.
     *
     * @param int $siteId
     * @param int $contentId
     * @param string $type
     * @param int $targetSiteId
     * @return int
     * @throws NonexistentTable
     */
    public function contentIdForSite(
        int $siteId,
        int $contentId,
        string $type,
        int $targetSiteId
    ): int;

    /**
     * Returns the content IDs for the given relationship ID.
     *
     * @param int $relationshipId
     * @return int[]
     * @throws NonexistentTable
     */
    public function contentIds(int $relationshipId): array;

    /**
     * Returns all relations for the given content element.
     *
     * @param int $siteId
     * @param int $contentId
     * @param string $type
     * @return int[]
     * @throws NonexistentTable
     */
    public function relations(int $siteId, int $contentId, string $type): array;

    /**
     * Returns the relationship ID for the given arguments.
     *
     * @param int[] $contentIds
     * @param string $type
     * @param bool $create
     * @return int
     * @throws NonexistentTable
     */
    public function relationshipId(array $contentIds, string $type, bool $create = false): int;

    /**
     * Checks if the site with the given ID has any relations of the given (or any) content type.
     *
     * @param int $siteId
     * @param string $type
     * @return bool
     * @throws NonexistentTable
     */
    public function hasSiteRelations(int $siteId, string $type = ''): bool;

    /**
     * Relates all posts between the given source site and the given destination site.
     *
     * This method is suited to be used after site duplication, because both sites are assumed to
     * have the exact same post IDs.
     * Furthermore, the current site is assumed to be either the source site or the destination site.
     *
     * @param int $sourceSite
     * @param int $targetSite
     * @return bool
     * @throws NonexistentTable
     */
    public function relateAllPosts(int $sourceSite, int $targetSite): bool;

    /**
     * Relates all terms between the given source site and the given destination site.
     *
     * This method is suited to be used after site duplication, because both sites are assumed to
     * have the exact same term taxonomy IDs.
     * Furthermore, the current site is assumed to be either the source site or the destination site.
     *
     * @param int $sourceSite
     * @param int $targetSite
     * @return bool
     * @throws NonexistentTable
     */
    public function relateAllTerms(int $sourceSite, int $targetSite): bool;

    /**
     * Sets a relation according to the given arguments.
     *
     * @param int $relationshipId
     * @param int $siteId
     * @param int $contentId
     * @return bool
     * @throws NonexistentTable
     */
    public function saveRelation(int $relationshipId, int $siteId, int $contentId): bool;
}
