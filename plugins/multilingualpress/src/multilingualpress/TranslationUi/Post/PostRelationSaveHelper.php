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

namespace Inpsyde\MultilingualPress\TranslationUi\Post;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Attachment\Copier;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;

use function Inpsyde\MultilingualPress\resolve;

class PostRelationSaveHelper
{
    const FILTER_METADATA = 'multilingualpress.post_meta_data';
    const FILTER_SYNC_KEYS = 'multilingualpress.sync_post_meta_keys';
    const ACTION_BEFORE_SAVE_RELATIONS = 'multilingualpress.before_save_posts_relations';
    const ACTION_AFTER_SAVED_RELATIONS = 'multilingualpress.after_saved_posts_relations';

    /**
     * @var ContentRelations
     */
    private $contentRelations;

    public function __construct(ContentRelations $contentRelations)
    {
        $this->contentRelations = $contentRelations;
    }

    /**
     * @param RelationshipContext $context
     * @return int
     */
    public function relatedPostParent(RelationshipContext $context): int
    {
        static $parentIds = [];
        $remoteSiteId = $context->remoteSiteId();
        if (array_key_exists($remoteSiteId, $parentIds)) {
            return $parentIds[$remoteSiteId];
        }

        if (!is_post_type_hierarchical($context->sourcePost()->post_type)) {
            $parentIds[$remoteSiteId] = 0;

            return 0;
        }

        $parent = (int)$context->sourcePost()->post_parent;
        if (!$parent) {
            $parentIds[$remoteSiteId] = 0;

            return 0;
        }

        $sourceSiteId = $context->sourceSiteId();
        if ($sourceSiteId === $remoteSiteId) {
            return $parent;
        }

        $relatedParents = $this->contentRelations->relations(
            $sourceSiteId,
            $parent,
            ContentRelations::CONTENT_TYPE_POST
        );

        $parentIds[$remoteSiteId] = (int)($relatedParents[$remoteSiteId] ?? 0);

        return $parentIds[$remoteSiteId];
    }

    /**
     * Set the source id of the element.
     *
     * @param RelationshipContext $context
     * @return bool
     */
    public function relatePosts(RelationshipContext $context): bool
    {
        $sourceSiteId = $context->sourceSiteId();
        $sourcePostId = $context->sourcePostId();
        $remoteSiteId = $context->remoteSiteId();
        $remotePostId = $context->remotePostId();

        if ($sourceSiteId === $remoteSiteId) {
            return true;
        }

        if (!$context->hasRemotePost()) {
            return false;
        }

        $postIds = [
            $sourceSiteId => $sourcePostId,
            $remoteSiteId => $remotePostId,
        ];

        $relationshipId = $this->contentRelations->relationshipId(
            $postIds,
            ContentRelations::CONTENT_TYPE_POST,
            true
        );

        if (!$relationshipId) {
            return false;
        }

        /**
         * Before save relations.
         *
         * @param RelationshipContext $context The context of the relationship.
         * @param int $relationshipId The Id of the relation.
         */
        do_action(self::ACTION_BEFORE_SAVE_RELATIONS, $context, $relationshipId);

        foreach ($postIds as $siteId => $postId) {
            if (!$this->contentRelations->saveRelation($relationshipId, $siteId, $postId)) {
                return false;
            }
        }

        /**
         * After saved relations.
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

        if ($sourceSiteId === $remoteSiteId || !$context->hasRemotePost()) {
            return;
        }

        $originalSiteId = $this->maybeSwitchSite($sourceSiteId);

        /**
         * Filters the post meta keys that has to be sync from source post.
         *
         * Important: executes on the context of *source* post.
         *
         * @param array $keysToSync
         * @param RelationshipContext $context
         */
        $keysToSync = apply_filters(self::FILTER_SYNC_KEYS, [], $context, $request);
        if (!$keysToSync || !is_array($keysToSync)) {
            $keysToSync = [];
        }

        $sourcePostId = $context->sourcePostId();
        $valuesToSync = [];
        foreach ($keysToSync as $key) {
            if (is_string($key) && $key) {
                $valuesToSync[$key] = get_post_meta($sourcePostId, $key, false);
            }
        }

        $this->maybeRestoreSite($originalSiteId);

        $originalSiteId = $this->maybeSwitchSite($remoteSiteId);

        /**
         * Filters the post meta data that to save on remote post.
         *
         * Important: executes on the context of *remote* post.
         *
         * @param array $keysToSync
         * @param RelationshipContext $context
         */
        $valuesToSync = apply_filters(self::FILTER_METADATA, $valuesToSync, $context);
        if (!$valuesToSync || !is_array($valuesToSync)) {
            return;
        }
        $remotePostId = $context->remotePostId();
        foreach ($valuesToSync as $key => $values) {
            if (!is_string($key) || !$key) {
                continue;
            }

            delete_post_meta($remotePostId, $key);
            foreach ((array)$values as $value) {
                update_post_meta($remotePostId, $key, $value);
            }
        }

        $this->maybeRestoreSite($originalSiteId);
    }

    /**
     * @param RelationshipContext $context
     * @return bool
     */
    public function syncThumb(RelationshipContext $context): bool
    {
        $sourceSiteId = $context->sourceSiteId();
        $remoteSiteId = $context->remoteSiteId();
        $remotePostId = $context->remotePostId();
        $sourcePostId = $context->sourcePostId();

        if ($sourceSiteId === $remoteSiteId || !$remotePostId) {
            return true;
        }

        $copier = resolve(Copier::class);

        $originalSiteId = $this->maybeSwitchSite($sourceSiteId);

        $copiedIds = [];
        $thumbnailId = (int)get_post_thumbnail_id($sourcePostId);
        $thumbnailId and $copiedIds = $copier->copyById(
            $sourceSiteId,
            $remoteSiteId,
            [$thumbnailId]
        );

        $this->maybeRestoreSite($originalSiteId);

        if (!$copiedIds) {
            return false;
        }

        $result = (bool)update_post_meta($remotePostId, '_thumbnail_id', $copiedIds[0]);
        $result and $result = wp_update_post([
            'ID' => $copiedIds[0],
            'post_parent' => $remotePostId,
        ], true);

        if (is_wp_error($result)) {
            return false;
        }

        return (bool)$result;
    }

    /**
     * Sync terms from source post to remote post.
     *
     * @param RelationshipContext $context
     * @return bool
     */
    public function syncTaxonomyTerms(RelationshipContext $context): bool
    {
        $sourceSiteId = $context->sourceSiteId();
        $remoteSiteId = $context->remoteSiteId();
        $sourcePost = $context->sourcePost();
        $remotePost = $context->remotePost();
        $errors = 0;

        if ($sourceSiteId === $remoteSiteId || !$context->hasRemotePost()) {
            return true;
        }

        $originalSiteId = $this->maybeSwitchSite($sourceSiteId);
        $sourceTaxData = MetaboxFields::taxonomiesAndTermsOf($sourcePost);
        $sourceTerms = wp_get_object_terms($sourcePost->ID, array_keys($sourceTaxData));

        $this->maybeRestoreSite($originalSiteId);

        $originalSiteId = $this->maybeSwitchSite($remoteSiteId);

        $remoteTaxData = MetaboxFields::taxonomiesAndTermsOf($remotePost);
        $remoteTerms = wp_get_object_terms($remotePost->ID, array_keys($remoteTaxData));

        $toRemoveTaxonomies = array_keys(array_diff_key($remoteTaxData, $sourceTaxData));
        foreach ($toRemoveTaxonomies as $toRemoveTaxonomy) {
            $remove = wp_set_object_terms($remotePost->ID, [], $toRemoveTaxonomy, false);
            is_wp_error($remove) and $errors++;
        }

        $toRemoveTerms = array_diff_key($remoteTerms, $sourceTerms);
        foreach ($toRemoveTerms as $toRemoveTerm) {
            $remove = wp_set_object_terms($remotePost->ID, [], $toRemoveTerm->taxonomy, false);
            is_wp_error($remove) and $errors++;
        }

        $toSync = [];

        /** @var \WP_Term $term */
        foreach ($sourceTerms as $term) {
            $remoteTermTaxId = $this->contentRelations->contentIdForSite(
                $sourceSiteId,
                $term->term_taxonomy_id,
                ContentRelations::CONTENT_TYPE_TERM,
                $remoteSiteId
            );

            $term = $remoteTermTaxId ? get_term_by('term_taxonomy_id', $remoteTermTaxId) : null;
            if ($term instanceof \WP_Term) {
                array_key_exists($term->taxonomy, $toSync) or $toSync[$term->taxonomy] = [];
                $toSync[$term->taxonomy][] = (int)$term->term_id;
            }
        }

        foreach ($toSync as $toSyncTaxonomy => $toSyncTermIds) {
            $set = wp_set_object_terms($remotePost->ID, $toSyncTermIds, $toSyncTaxonomy, false);
            $set or $errors++;
        }

        $this->maybeRestoreSite($originalSiteId);

        return 0 === $errors;
    }

    /**
     * @param int $remoteSiteId
     * @return int
     */
    private function maybeSwitchSite(int $remoteSiteId): int
    {
        $currentSite = get_current_blog_id();
        if ($currentSite !== $remoteSiteId) {
            switch_to_blog($remoteSiteId);

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
