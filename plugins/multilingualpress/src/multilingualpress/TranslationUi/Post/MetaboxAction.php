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

use Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes;
use Inpsyde\MultilingualPress\Framework\Admin\AdminNotice;
use Inpsyde\MultilingualPress\Framework\Admin\Metabox;
use Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;

use function Inpsyde\MultilingualPress\translationIds;

/**
 * Class MetaboxAction
 */
final class MetaboxAction implements Metabox\Action
{
    // phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
    const FILTER_TAXONOMIES_SLUGS_BEFORE_REMOVE = 'multilingualpress.taxonomies_slugs_before_remove';
    const FILTER_NEW_RELATE_REMOTE_POST_BEFORE_INSERT = 'multilingualpress.new_relate_remote_post_before_insert';
    const ACTION_METABOX_AFTER_RELATE_POSTS = 'multilingualpress.metabox_after_relate_posts';
    const ACTION_METABOX_BEFORE_UPDATE_REMOTE_POST = 'multilingualpress.metabox_before_update_remote_post';
    const ACTION_METABOX_AFTER_UPDATE_REMOTE_POST = 'multilingualpress.metabox_after_update_remote_post';
    // phpcs:enable

    /**
     * @var array
     */
    private static $calledCount = [];

    /**
     * @var MetaboxFields
     */
    private $fields;

    /**
     * @var MetaboxFieldsHelper
     */
    private $fieldsHelper;

    /**
     * @var RelationshipContext
     */
    private $relationshipContext;

    /**
     * @var ActivePostTypes
     */
    private $postTypes;

    /**
     * @var ContentRelations
     */
    private $contentRelations;

    /**
     * @var SourcePostSaveContext
     */
    private $sourcePostContext;

    /**
     * @param MetaboxFields $fields
     * @param MetaboxFieldsHelper $fieldsHelper
     * @param RelationshipContext $relationshipContext
     * @param ActivePostTypes $postTypes
     * @param ContentRelations $contentRelations
     */
    public function __construct(
        MetaboxFields $fields,
        MetaboxFieldsHelper $fieldsHelper,
        RelationshipContext $relationshipContext,
        ActivePostTypes $postTypes,
        ContentRelations $contentRelations
    ) {

        $this->fields = $fields;
        $this->fieldsHelper = $fieldsHelper;
        $this->relationshipContext = $relationshipContext;
        $this->postTypes = $postTypes;
        $this->contentRelations = $contentRelations;
    }

    /**
     * @inheritdoc
     */
    public function save(Request $request, PersistentAdminNotices $notices): bool
    {
        $relation = $this->saveOperation($request);
        if (!$relation) {
            return false;
        }

        if (!$this->isValidSaveRequest($this->sourceContext($request))) {
            return false;
        }

        $relationshipHelper = new PostRelationSaveHelper($this->contentRelations);

        return $this->doSaveOperation($relation, $request, $relationshipHelper, $notices);
    }

    /**
     * @param Request $request
     * @return string
     */
    private function saveOperation(Request $request): string
    {
        $relation = $this->fieldsHelper->fieldRequestValue($request, MetaboxFields::FIELD_RELATION);

        if (
            $relation !== MetaboxFields::FIELD_RELATION_NEW
            && $relation !== MetaboxFields::FIELD_RELATION_LEAVE
        ) {
            return '';
        }

        $hasRemotePost = $this->relationshipContext->hasRemotePost();

        if (
            ($relation === MetaboxFields::FIELD_RELATION_NEW && $hasRemotePost)
            || ($relation === MetaboxFields::FIELD_RELATION_LEAVE && !$hasRemotePost)
        ) {
            return '';
        }

        return $relation;
    }

    /**
     * @param Request $request
     * @return SourcePostSaveContext
     */
    private function sourceContext(Request $request): SourcePostSaveContext
    {
        if ($this->sourcePostContext) {
            return $this->sourcePostContext;
        }

        switch_to_blog($this->relationshipContext->sourceSiteId());
        $this->sourcePostContext = new SourcePostSaveContext(
            $this->relationshipContext->sourcePost(),
            $this->postTypes,
            $request
        );
        restore_current_blog();

        return $this->sourcePostContext;
    }

    /**
     * Generate the remote post data
     *
     * @param array<string, scalar|null> $values A map of
     * {@link https://developer.wordpress.org/reference/classes/wp_post/ WP_Post} data field names to values
     * @param PostRelationSaveHelper $relationshipHelper
     * @return array<string, scalar|null> A map of
     * {@link https://developer.wordpress.org/reference/classes/wp_post/ WP_Post} data field names to values
     * @throws NonexistentTable
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     * phpcs:disable Generic.Metrics.CyclomaticComplexity.TooHigh
     */
    private function generatePostData(
        array $values,
        PostRelationSaveHelper $relationshipHelper
    ): array {

        // phpcs:enable

        $sourceSiteId = $this->relationshipContext->sourceSiteId();
        $remoteSiteId = $this->relationshipContext->remoteSiteId();
        $source = $this->relationshipContext->sourcePost();
        $hasRemote = $this->relationshipContext->hasRemotePost();

        $changedFieldsMeta = $values[MetaboxFields::FIELD_CHANGED_FIELDS] ?? '';
        $changedFields = explode(',', $changedFieldsMeta);

        $title = $values[MetaboxFields::FIELD_TITLE] ?? '';
        $newRemotePostTitleIsEmpty = $this->newRemotePostFieldIsEmpty($title, $hasRemote);
        if ($newRemotePostTitleIsEmpty) {
            $title = $source->post_title;
        }

        $slug = $values[MetaboxFields::FIELD_SLUG] ?? '';
        $newRemotePostSlugIsEmpty = $this->newRemotePostFieldIsEmpty($slug, $hasRemote);
        if ($newRemotePostSlugIsEmpty) {
            $slug = sanitize_title($title);
        }

        $status = $this->maybeChangePostStatus($values[MetaboxFields::FIELD_STATUS], $hasRemote) ?? '';
        $newRemotePostStatusIsEmpty = $this->newRemotePostFieldIsEmpty($status, $hasRemote);
        if ($newRemotePostStatusIsEmpty) {
            $status = 'draft';
        }

        $excerpt = $values[MetaboxFields::FIELD_EXCERPT] ?? '';

        $post = [];
        $hasRemote and $post['ID'] = $this->relationshipContext->remotePostId();

        if ($newRemotePostTitleIsEmpty || $this->isFieldChanged(MetaboxFields::FIELD_TITLE, $changedFields)) {
            $post['post_title'] = $title;
        }

        if ($newRemotePostSlugIsEmpty || $this->isFieldChanged(MetaboxFields::FIELD_SLUG, $changedFields)) {
            $post['post_name'] = $slug;
        }

        if ($newRemotePostStatusIsEmpty || $this->isFieldChanged(MetaboxFields::FIELD_STATUS, $changedFields)) {
            $post['post_status'] = $status;
        }

        if ($this->isFieldChanged(MetaboxFields::FIELD_EXCERPT, $changedFields)) {
            $post['post_excerpt'] = $excerpt;
        }

        if ($values[MetaboxFields::FIELD_COPY_CONTENT] ?? false) {
            switch_to_blog($sourceSiteId);
            $sourcePostContent = $this->handleReusableBlocks($source->ID, $source->post_content, $sourceSiteId, $remoteSiteId);
            restore_current_blog();
            $post['post_content'] = $sourcePostContent;
        }

        if ($status === 'future') {
            $post['post_date'] = $this->relationshipContext->sourcePost()->post_date;
            $post['post_date_gmt'] = $this->relationshipContext->sourcePost()->post_date_gmt;
        }

        if (!$hasRemote) {
            $base = $source;
            $post['post_parent'] = $relationshipHelper->relatedPostParent($this->relationshipContext);
            $post['post_type'] = $base->post_type;
            $post['post_author'] = $base->post_author;
            $post['comment_status'] = $base->comment_status;
            $post['ping_status'] = $base->ping_status;
            $post['post_password'] = $base->post_password;
            $post['menu_order'] = $base->menu_order;
        }

        return $post;
    }

    /**
     * @param string $operation
     * @param Request $request
     * @param PostRelationSaveHelper $relationshipHelper
     * @param PersistentAdminNotices $notices
     * @return bool
     */
    private function doSaveOperation(
        string $operation,
        Request $request,
        PostRelationSaveHelper $relationshipHelper,
        PersistentAdminNotices $notices
    ): bool {

        $values = $this->allFieldsValues($request);
        $post = $this->generatePostData($values, $relationshipHelper);

        if (!$post) {
            return false;
        }

        $postId = $this->savePost(
            $operation,
            $post,
            $relationshipHelper,
            $request,
            $notices
        );

        if (!$postId) {
            // translators: %s is the language name
            $message = __(
                'Error updating translation for %s: error updating post in database.',
                'multilingualpress'
            );
            $notices->add(AdminNotice::error($message));

            return false;
        }

        $relationshipHelper->syncMetadata($this->relationshipContext, $request);

        if ($values[MetaboxFields::FIELD_COPY_FEATURED] ?? false) {
            $relationshipHelper->syncThumb($this->relationshipContext);
        }

        $syncTaxonomies = $values[MetaboxFields::FIELD_COPY_TAXONOMIES] ?? false;
        $terms = $syncTaxonomies ? [] : ($values[MetaboxFields::FIELD_TAXONOMIES] ?? []);
        $slugs = $values[MetaboxFields::FIELD_TAXONOMY_SLUGS] ?? [];

        if ($syncTaxonomies) {
            $relationshipHelper->syncTaxonomyTerms($this->relationshipContext);
        }

        if (!$syncTaxonomies && ($terms || $slugs) && $this->relationshipContext->hasRemotePost()) {
            $this->saveTaxonomyTerms(array_intersect_key($terms, array_flip($slugs)), $slugs ? array_fill_keys($slugs, 1) : []);
        }

        return false;
    }

    /**
     * Check if the current request should be processed by save().
     *
     * @param SourcePostSaveContext $context
     * @return bool
     */
    private function isValidSaveRequest(SourcePostSaveContext $context): bool
    {
        $site = $this->relationshipContext->remoteSiteId();
        array_key_exists($site, self::$calledCount) or self::$calledCount[$site] = 0;

        // For auto-drafts, 'save_post' is called twice, resulting in doubled drafts for translations.
        self::$calledCount[$site]++;

        return
            $context->postType()
            && $context->postStatus()
            && ($context->postStatus() !== 'auto-draft' || self::$calledCount[$site] === 1);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function allFieldsValues(Request $request): array
    {
        $fields = [];
        $allTabs = $this->fields->allFieldsTabs($this->relationshipContext);
        /** @var MetaboxTab $tab */
        foreach ($allTabs as $tab) {
            $fields += $this->tabFieldsValues($tab, $request);
        }

        $changedFields = $this->fields->changedFieldsField();
        $fields[$changedFields->key()] = $changedFields->requestValue($request, $this->fieldsHelper);

        return $fields;
    }

    /**
     * @param MetaboxTab $tab
     * @param Request $request
     * @return array
     */
    private function tabFieldsValues(MetaboxTab $tab, Request $request): array
    {
        $fields = [];
        if (!$tab->enabled($this->relationshipContext)) {
            return $fields;
        }

        $tabFields = $tab->fields();
        foreach ($tabFields as $field) {
            if ($field->enabled($this->relationshipContext)) {
                $fields[$field->key()] = $field->requestValue($request, $this->fieldsHelper);
            }
        }

        return $fields;
    }

    /**
     * @param string $operation
     * @param array $post
     * @param PostRelationSaveHelper $helper
     * @param Request $request
     * @param PersistentAdminNotices $notices
     * @return int
     */
    private function savePost(
        string $operation,
        array $post,
        PostRelationSaveHelper $helper,
        Request $request,
        PersistentAdminNotices $notices
    ): int {

        /**
         * Performs an action before the post has been updated
         *
         * @param RelationshipContext $relationshipContext
         * @param array $post
         * @param string $operation
         */
        do_action(
            self::ACTION_METABOX_BEFORE_UPDATE_REMOTE_POST,
            $this->relationshipContext,
            $post,
            $operation
        );

        if ($operation === MetaboxFields::FIELD_RELATION_NEW) {
            /**
             * Filter Remote post
             *
             * @param array $post The remote post object
             * @param RelationshipContext $relationshipContext
             * @param string $operation
             */
            $post = (array)apply_filters(
                self::FILTER_NEW_RELATE_REMOTE_POST_BEFORE_INSERT,
                $post,
                $this->relationshipContext,
                $operation
            );
        }

        $postId = $operation === MetaboxFields::FIELD_RELATION_NEW
            ? wp_insert_post(wp_slash($post), true)
            : wp_update_post(wp_slash($post), true);

        /**
         * Performs an action after the post has been updated
         *
         * @param RelationshipContext $relationshipContext
         * @param array $post
         * @param string $operation
         */
        do_action(
            self::ACTION_METABOX_AFTER_UPDATE_REMOTE_POST,
            $this->relationshipContext,
            $post,
            $operation
        );

        if (!is_numeric($postId) || !$postId) {
            return 0;
        }

        $remotePost = get_post($postId);
        if (!$remotePost instanceof \WP_Post) {
            return 0;
        }

        $this->relationshipContext = RelationshipContext::fromExistingAndData(
            $this->relationshipContext,
            [RelationshipContext::REMOTE_POST_ID => $postId]
        );

        if (!$helper->relatePosts($this->relationshipContext)) {
            return 0;
        }

        /**
         * Perform action after the post relations have been created
         *
         * @param RelationshipContext $relationshipContext
         * @param Request $request
         * @param PersistentAdminNotices $notices
         * @param string $operation
         */
        do_action(
            self::ACTION_METABOX_AFTER_RELATE_POSTS,
            $this->relationshipContext,
            $request,
            $notices,
            $operation
        );

        return (int)$remotePost->ID;
    }

    /**
     * @param array $taxonomyTerms
     * @param array $taxonomies
     */
    private function saveTaxonomyTerms(array $taxonomyTerms, array $taxonomies)
    {
        $post = $this->relationshipContext->remotePost();

        foreach ($taxonomyTerms as $taxonomy => $termIds) {
            sort($termIds);
            $currentTerms = get_the_terms($post, $taxonomy);
            if (is_wp_error($currentTerms)) {
                continue;
            }
            $currentTermIds = [];
            if (!is_array($currentTerms)) {
                $currentTerms = [];
            }
            if ($currentTerms) {
                $currentTermIds = wp_parse_id_list(array_column($currentTerms, 'term_id'));
                $currentTermIds and sort($currentTermIds);
            }

            unset($taxonomies[$taxonomy]);
            if ($currentTermIds === $termIds) {
                continue;
            }

            wp_set_object_terms($post->ID, $termIds, $taxonomy, false);
        }

        /**
         * Filter Taxonomies before remove connection between post and terms
         *
         * @param array $taxonomies A list of taxonomies where key is the name and the value
         * is a boolean that indicate if the terms for the taxonomy must be removed or not.
         */
        $taxonomies = (array)apply_filters(
            self::FILTER_TAXONOMIES_SLUGS_BEFORE_REMOVE,
            $taxonomies
        );

        foreach ($taxonomies as $taxonomy => $remove) {
            if (!$remove) {
                continue;
            }

            $taxonomyObject = get_taxonomy($taxonomy);

            if (
                !$taxonomyObject
                || !current_user_can($taxonomyObject->cap->delete_terms, $taxonomy)
            ) {
                continue;
            }

            wp_set_object_terms($post->ID, [], $taxonomy, false);
        }
    }

    /**
     * Changes post status if condition match
     *
     * @param string $status
     * @param bool $hasRemote
     * @return string
     */
    private function maybeChangePostStatus(string $status, bool $hasRemote): string
    {
        if ($status === 'none' && $hasRemote) {
            $status = $this->relationshipContext->remotePost()->post_status;
        }
        if (!$status && $hasRemote) {
            $status = $this->relationshipContext->remotePost()->post_status;
        }

        return $status;
    }

    /**
     * Replace the reusable blocks.
     *
     * If reusable gutenberg block exists in source post content and if it is connected with the reusable block in
     * remote site, then we need to replace it's id with the id of remote block
     *
     * @param int $sourcePostId The source post id
     * @param string $sourcePostContent The source post content
     * @param int $sourceSiteId The source site id
     * @param int $remoteSiteId The remote site id
     * @return string The post content with replaced reusable block ids from remote site
     * @throws NonexistentTable
     */
    protected function handleReusableBlocks(
        int $sourcePostId,
        string $sourcePostContent,
        int $sourceSiteId,
        int $remoteSiteId
    ): string {

        if (!has_block('block', $sourcePostId)) {
            return $sourcePostContent;
        }

        $blocks = parse_blocks($sourcePostContent);

        if (!is_array($blocks) || empty($blocks)) {
            return $sourcePostContent;
        }

        $blocksToBeReplaced = [];
        $replaceWithBlock = [];
        foreach ($blocks as $block) {
            if ($block['blockName'] !== 'core/block' || empty($block['attrs']['ref'])) {
                continue;
            }

            $translationIds = translationIds($block['attrs']['ref'], 'post', $sourceSiteId);
            if (empty($translationIds) || !isset($translationIds[$remoteSiteId])) {
                continue;
            }

            $blocksToBeReplaced[] = serialize_block($block);
            $block['attrs']['ref'] = $translationIds[$remoteSiteId];
            $replaceWithBlock[] = serialize_block($block);
        }

        return str_replace($blocksToBeReplaced, $replaceWithBlock, $sourcePostContent);
    }

    /**
     * Check if the field with given name is changed
     *
     * @param string $field The field name to check
     * @param array $changedFields The list of changed fields
     * @return bool Whether the field is changed
     */
    protected function isFieldChanged(string $field, array $changedFields): bool
    {
        return in_array($field, $changedFields, true);
    }

    /**
     * Check if the field with given name was not set when creating a new connection
     *
     * @param string $field The field name to check
     * @param bool $hasRemote whether the current post is already connected
     * @return bool Whether the field was not set when a new connection is created
     */
    protected function newRemotePostFieldIsEmpty(string $field, bool $hasRemote): bool
    {
        return !$field && !$hasRemote;
    }
}
