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

namespace Inpsyde\MultilingualPress\TranslationUi\Term;

use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Http\Request;

class TermRelationSaveHelper
{
    const FILTER_METADATA = 'multilingualpress.term_meta_data';
    const FILTER_SYNC_META_KEYS = 'multilingualpress.sync_term_meta_keys';
    const ACTION_BEFORE_SAVE_RELATIONS = 'multilingualpress.before_save_terms_relations';
    const ACTION_AFTER_SAVED_RELATIONS = 'multilingualpress.after_saved_terms_relations';

    /**
     * @var ContentRelations
     */
    private $contentRelations;

    /**
     * @param ContentRelations $contentRelations
     */
    public function __construct(ContentRelations $contentRelations)
    {
        $this->contentRelations = $contentRelations;
    }

    /**
     * @param RelationshipContext $context
     * @return int
     */
    public function relatedTermParent(RelationshipContext $context): int
    {
        static $parentIds = [];
        $remoteSiteId = $context->remoteSiteId();
        if (array_key_exists($remoteSiteId, $parentIds)) {
            return $parentIds[$remoteSiteId];
        }

        $taxonomy = $context->sourceTerm()->taxonomy;

        if (!is_taxonomy_hierarchical($taxonomy)) {
            $parentIds[$remoteSiteId] = 0;

            return 0;
        }

        $sourceParentId = (int)$context->sourceTerm()->parent;
        $sourceSiteId = $context->sourceSiteId();

        if ($sourceSiteId === $remoteSiteId) {
            $parentIds[$remoteSiteId] = (int)$sourceParentId;

            return $parentIds[$remoteSiteId];
        }

        $sourceParentTerm = null;
        if ($sourceParentId > 0) {
            $originalSiteId = $this->maybeSwitchSite($sourceSiteId);
            $sourceParentTerm = get_term($sourceParentId, $taxonomy);
            $this->maybeRestoreSite($originalSiteId);
        }

        if (!$sourceParentTerm instanceof \WP_Term) {
            $parentIds[$remoteSiteId] = 0;

            return $parentIds[$remoteSiteId];
        }

        $relatedParentsTermTaxonomyIds = $this->contentRelations->relations(
            $sourceSiteId,
            $sourceParentTerm->term_taxonomy_id,
            ContentRelations::CONTENT_TYPE_TERM
        );

        $relatedParentTermTaxonomyId = (int)($relatedParentsTermTaxonomyIds[$remoteSiteId] ?? 0);

        if (!$relatedParentTermTaxonomyId) {
            $parentIds[$remoteSiteId] = 0;

            return $parentIds[$remoteSiteId];
        }

        $originalSiteId = $this->maybeSwitchSite($remoteSiteId);
        $relatedParentTerm = get_term_by('term_taxonomy_id', $relatedParentTermTaxonomyId);
        $this->maybeRestoreSite($originalSiteId);

        $parentIds[$remoteSiteId] = $relatedParentTerm instanceof \WP_Term
            ? (int)$relatedParentTerm->term_id
            : 0;

        return $parentIds[$remoteSiteId];
    }

    /**
     * @param RelationshipContext $context
     * @return bool
     */
    public function relateTerms(RelationshipContext $context): bool
    {
        $sourceSiteId = $context->sourceSiteId();
        $remoteSiteId = $context->remoteSiteId();
        $sourceTermTaxonomyId = $context->sourceTermId();
        $remoteTermTaxonomyId = $context->remoteTermId();

        if ($sourceSiteId === $remoteSiteId) {
            return true;
        }

        if (!$context->hasRemoteTerm()) {
            return false;
        }

        $contentIds = [
            $sourceSiteId => $sourceTermTaxonomyId,
            $remoteSiteId => $remoteTermTaxonomyId,
        ];

        $relationshipId = $this->contentRelations->relationshipId(
            $contentIds,
            ContentRelations::CONTENT_TYPE_TERM,
            true
        );
        if (!$relationshipId) {
            return false;
        }

        $contentIds[$remoteSiteId] = $remoteTermTaxonomyId;

        /**
         * Before save relations
         *
         * @param RelationshipContext $context The context of the relationship.
         * @param int $relationshipId The Id of the relation.
         */
        do_action(self::ACTION_BEFORE_SAVE_RELATIONS, $context, $relationshipId);

        foreach ($contentIds as $siteId => $termTaxonomyId) {
            if (!$this->contentRelations->saveRelation($relationshipId, $siteId, $termTaxonomyId)) {
                return false;
            }
        }

        /**
         * After saved relations
         *
         * @param RelationshipContext $context The context of the relationship.
         * @param int $relationshipId The Id of the relation.
         */
        do_action(self::ACTION_AFTER_SAVED_RELATIONS, $context, $relationshipId);

        return true;
    }

    /**
     * @param RelationshipContext $context
     * @param Request $request
     */
    public function syncMetadata(RelationshipContext $context, Request $request)
    {
        $sourceSiteId = $context->sourceSiteId();
        $remoteSiteId = $context->remoteSiteId();

        if ($sourceSiteId === $remoteSiteId || !$context->hasRemoteTerm()) {
            return;
        }

        $originalSiteId = $this->maybeSwitchSite($sourceSiteId);

        /**
         * Filters the term meta keys that has to be sync from source term.
         *
         * Important: executes on the context of *source* site.
         *
         * @param array $keysToSync
         * @param RelationshipContext $context
         */
        $keysToSync = apply_filters(self::FILTER_SYNC_META_KEYS, [], $context, $request);
        if (!$keysToSync || !is_array($keysToSync)) {
            $keysToSync = [];
        }

        $sourceTermId = $context->sourceTerm()->term_id;
        $valuesToSync = [];
        foreach ($keysToSync as $key) {
            if (is_string($key) && $key) {
                $valuesToSync[$key] = get_term_meta($sourceTermId, $key, false);
            }
        }

        $this->maybeRestoreSite($originalSiteId);

        $originalSiteId = $this->maybeSwitchSite($remoteSiteId);

        /**
         * Filters the term meta data that to save on remote term.
         *
         * Important: executes on the context of *remote* term.
         *
         * @param array $keysToSync
         * @param RelationshipContext $context
         */
        $valuesToSync = apply_filters(self::FILTER_METADATA, $valuesToSync, $context);
        if (!$valuesToSync || !is_array($valuesToSync)) {
            return;
        }

        $remoteTermId = $context->remoteTerm()->term_id;
        foreach ($valuesToSync as $key => $values) {
            if (!is_string($key) || !$key) {
                continue;
            }
            delete_term_meta($remoteTermId, $key);
            foreach ((array)$values as $value) {
                update_term_meta($remoteTermId, $key, $value);
            }
        }

        $this->maybeRestoreSite($originalSiteId);
    }

    /**
     * @param int $targetSiteId
     * @return int
     */
    private function maybeSwitchSite(int $targetSiteId): int
    {
        $currentSite = get_current_blog_id();
        if ($currentSite !== $targetSiteId) {
            switch_to_blog($targetSiteId);

            return $currentSite;
        }

        return -1;
    }

    /**
     * @param int $originalSiteId
     * @return bool
     */
    private function maybeRestoreSite(int $originalSiteId): bool
    {
        if ($originalSiteId < 0) {
            return false;
        }

        restore_current_blog();

        $currentSite = get_current_blog_id();
        if ($currentSite !== $originalSiteId) {
            switch_to_blog($originalSiteId);
        }

        return true;
    }
}
