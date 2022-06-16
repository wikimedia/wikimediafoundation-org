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
 * Interface for all site relations API implementations.
 */
interface SiteRelations
{
    const RELATED_SITE_IDS_CACHE_KEY = 'relatedSiteIds';
    const ALL_RELATIONS_CACHE_KEY = 'allRelations';

    /**
     * Deletes the relationship between the given sites. If only one site is given, all its relations
     * will be deleted.
     *
     * @param int $sourceSite
     * @param int $targetSite
     * @return int
     * @throws NonexistentTable
     */
    public function deleteRelation(int $sourceSite, int $targetSite = 0): int;

    /**
     * Returns an array with site IDs as keys and arrays with the IDs of all related sites as values.
     *
     * @return int[]
     * @throws NonexistentTable
     */
    public function allRelations(): array;

    /**
     * Returns an array holding the IDs of all sites related to the site with the given ID.
     *
     * @param int $siteId
     * @param bool $includeSite
     * @return int[]
     * @throws NonexistentTable
     */
    public function relatedSiteIds(int $siteId, bool $includeSite = false): array;

    /**
     * Creates relations between one site and one or more other sites.
     *
     * @param int $baseSiteId
     * @param int[] $siteIds
     * @return int
     * @throws NonexistentTable
     */
    public function insertRelations(int $baseSiteId, array $siteIds): int;

    /**
     * Sets the relations for the site with the given ID.
     *
     * @param int $baseSiteId
     * @param int[] $siteIds
     * @return int
     * @throws NonexistentTable
     */
    public function relateSites(int $baseSiteId, array $siteIds): int;
}
