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

namespace Inpsyde\MultilingualPress\TranslationUi\Term\Ajax;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext;

class Search
{
    const ACTION = 'multilingualpress_remote_term_search_arguments';
    const SEARCH_PARAM = 'search';
    const FILTER_REMOTE_ARGUMENTS = 'multilingualpress.remote_term_search_arguments';

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

        wp_send_json_success($this->findTerm($searchQuery, $context));
    }

    /**
     * @param string $searchQuery
     * @param RelationshipContext $context
     * @return array
     */
    public function findTerm(
        string $searchQuery,
        RelationshipContext $context
    ): array {

        $args = [
            'taxonomy' => $context->sourceTerm()->taxonomy,
            'hide_empty' => false,
            'search' => rtrim($searchQuery),
        ];

        if ($context->hasRemoteTerm()) {
            $args['exclude'] = [$context->remoteTerm()->term_id];
        }

        /**
         * Filters the query arguments for the remote term search.
         *
         * @param array $args
         */
        $args = (array)apply_filters(self::FILTER_REMOTE_ARGUMENTS, $args);

        switch_to_blog($context->remoteSiteId());

        $query = new \WP_Term_Query();
        $terms = $query->query($args);
        restore_current_blog();

        return array_map([$this, 'formatTerm'], $terms);
    }

    /**
     * @param \WP_Term $term
     * @return array
     */
    private function formatTerm(\WP_Term $term): array
    {
        return [
            'id' => (int)$term->term_taxonomy_id,
            'title' => $term->name,
        ];
    }
}
