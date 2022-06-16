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

class Term
{
    const ACTION = 'multilingualpress_remote_terms';
    const TAXONOMIES = 'taxonomies';

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

        $context = $this->contextBuilder->build();
        switch_to_blog($context->remoteSiteId());

        $taxonomies = $this->taxNameFromRequest();
        if (empty($taxonomies)) {
            wp_send_json_error('Missing data.');
        }

        $allTerms = [];
        foreach ($taxonomies as $taxonomy) {
            $allTerms[$taxonomy] = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
        }

        if (empty($allTerms)) {
            wp_send_json_error('No terms found.');
        }

        restore_current_blog();

        wp_send_json_success($allTerms);
    }

    /**
     * The Method is used to return current editing post taxonomy name from request
     *
     * @return array Taxonomy name of current editing post
     */
    protected function taxNameFromRequest(): array
    {
        return (array)$this->request->bodyValue(
            self::TAXONOMIES,
            INPUT_POST,
            FILTER_DEFAULT,
            FILTER_REQUIRE_ARRAY
        );
    }
}
