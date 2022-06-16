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

namespace Inpsyde\MultilingualPress\TranslationUi\Post\Ajax;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

class Search
{
    const ACTION = 'multilingualpress_remote_post_search';
    const SEARCH_PARAM = 'search';
    const FILTER_REMOTE_ARGUMENTS = 'multilingualpress.remote_post_search_arguments';

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ContextBuilder
     */
    private $contextBuilder;

    /**
     * @param Request $request
     * @param ContextBuilder $contextBuilder
     */
    public function __construct(Request $request, ContextBuilder $contextBuilder)
    {
        $this->request = $request;
        $this->contextBuilder = $contextBuilder;
    }

    /**
     * Handle AJAX request.
     */
    public function handle()
    {
        if (!wp_doing_ajax()) {
            return;
        }

        if (!doing_action('wp_ajax_' . self::ACTION)) {
            wp_send_json_error('Invalid action.');
        }

        $searchQuery = (string)$this->request->bodyValue(
            self::SEARCH_PARAM,
            INPUT_POST,
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_NO_ENCODE_QUOTES
        );

        if (!$searchQuery) {
            wp_send_json_error('Missing data.');
        }

        $context = $this->contextBuilder->build();

        wp_send_json_success($this->findPosts($searchQuery, $context));
    }

    /**
     * @param string $searchQuery
     * @param RelationshipContext $context
     * @return array
     */
    public function findPosts(
        string $searchQuery,
        RelationshipContext $context
    ): array {

        $args = [
            'post_type' => $context->sourcePost()->post_type,
            's' => $searchQuery,
            'posts_per_page' => 1000,
            'orderby' => 'relevance',
            'post_status' => [
                'draft',
                'future',
                'private',
                'publish',
            ],
        ];

        if ($context->hasRemotePost()) {
            $args['post__not_in'] = [$context->remotePost()->ID];
        }

        /**
         * Filters the query arguments for the remote post search.
         *
         * @param array $args
         */
        $args = (array)apply_filters(self::FILTER_REMOTE_ARGUMENTS, $args);

        switch_to_blog($context->remoteSiteId());
        $posts = get_posts($args);
        restore_current_blog();

        return array_map([$this, 'formatPost'], $posts);
    }

    /**
     * @param \WP_Post $post
     * @return array
     */
    private function formatPost(\WP_Post $post): array
    {
        return [
            'id' => (int)$post->ID,
            'title' => $post->post_title,
        ];
    }
}
