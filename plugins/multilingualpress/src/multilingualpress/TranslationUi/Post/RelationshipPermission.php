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

use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;

/**
 * Permission checker to be used to either permit or prevent access to posts.
 */
class RelationshipPermission
{
    const FILTER_IS_RELATED_POST_EDITABLE = 'multilingualpress.is_related_post_editable';

    /**
     * @var ContentRelations
     */
    private $contentRelations;

    /**
     * @var int[][]
     */
    private $relatedPosts = [];

    /**
     * @param ContentRelations $contentRelations
     */
    public function __construct(ContentRelations $contentRelations)
    {
        $this->contentRelations = $contentRelations;
    }

    /**
     * Checks if the current user can edit (or create) a post in the site with the given ID that is
     * related to given post in the current site.
     *
     * @param \WP_Post $post
     * @param int $relatedSiteId
     * @return bool
     */
    public function isRelatedPostEditable(\WP_Post $post, int $relatedSiteId): bool
    {
        $postType = get_post_type_object($post->post_type);
        if (!$postType instanceof \WP_Post_Type) {
            return false;
        }

        $relatedPost = $this->relatedPost($post, $relatedSiteId);
        $isPostEditable = $relatedPost
            ? current_user_can_for_blog($relatedSiteId, $postType->cap->edit_post, $relatedPost->ID)
            : current_user_can_for_blog($relatedSiteId, $postType->cap->create_posts);

        /**
         * Filters if the related post of the given post in the given site is editable.
         *
         * @param bool $isPostEditable
         * @param \WP_Post $post
         * @param int $relatedSiteId
         * @param int $relatedPostId
         */
        return (bool)apply_filters(
            self::FILTER_IS_RELATED_POST_EDITABLE,
            $isPostEditable,
            $post,
            $relatedSiteId,
            $relatedPost ? (int)$relatedPost->ID : 0
        );
    }

    /**
     * Returns the ID of the post in the site with the given ID that is related to given post in
     * the current site.
     *
     * @param \WP_Post $post
     * @param int $relatedSiteId
     * @return \WP_Post|null
     */
    private function relatedPost(\WP_Post $post, int $relatedSiteId)
    {
        $relatedPosts = $this->relatedPosts((int)$post->ID);
        if (empty($relatedPosts[$relatedSiteId])) {
            return null;
        }

        // This is just to be extra careful in case the post has been deleted via MySQL etc.
        $relatedPost = get_blog_post($relatedSiteId, $relatedPosts[$relatedSiteId]);

        return $relatedPost ?: null;
    }

    /**
     * Returns an array with the IDs of all related posts for the post with the given ID.
     *
     * @param int $postId
     * @return int[]
     */
    private function relatedPosts(int $postId): array
    {
        if (array_key_exists($postId, $this->relatedPosts)) {
            return $this->relatedPosts[$postId];
        }

        $this->relatedPosts[$postId] = $this->contentRelations->relations(
            get_current_blog_id(),
            $postId,
            ContentRelations::CONTENT_TYPE_POST
        );

        return $this->relatedPosts[$postId];
    }
}
