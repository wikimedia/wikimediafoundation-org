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

namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Ajax;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
use Inpsyde\MultilingualPress\TranslationUi\Post\Ajax\ContextBuilder;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;
use wpdb;

use function Inpsyde\MultilingualPress\resolve;

/**
 * Functionality for searching products in connected sites
 */
class Search
{
    use SwitchSiteTrait;

    const ACTION = 'multilingualpress_remote_post_search';
    const SEARCH_PARAM = 'search';
    const FILTER_PRODUCT_SEARCH_LIMIT = 'multilingualpress.filter_product_search_limit';

    /**
     * @var ContextBuilder
     */
    private $contextBuilder;

    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * @param ContextBuilder $contextBuilder
     * @param wpdb $wpdb
     */
    public function __construct(ContextBuilder $contextBuilder, wpdb $wpdb)
    {
        $this->contextBuilder = $contextBuilder;
        $this->wpdb = $wpdb;
    }

    /**
     * Handles the request and calls find products with the search terms
     * @param Request $request
     * @return void
     */
    public function handle(Request $request)
    {
        $searchQuery = (string)$request->bodyValue(
            self::SEARCH_PARAM,
            INPUT_POST,
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_NO_ENCODE_QUOTES
        );

        if (!$searchQuery) {
            wp_send_json_error(esc_html__('Missing data.', 'multilingualpress'));
            return;
        }

        wp_send_json_success($this->findProducts($searchQuery, $this->contextBuilder->build()));
    }

    /**
     * Returns an array with product objects based on the given search terms
     *
     * @param string $searchQuery
     * @param RelationshipContext $context
     * @return array
     */
    protected function findProducts(
        string $searchQuery,
        RelationshipContext $context
    ): array {

        $postType = $context->sourcePost()->post_type;
        if ($postType !== 'product') {
            return [];
        }

        $excludePostId = $context->hasRemotePost() ? $context->remotePost()->ID : 0;

        $previousSite = $this->maybeSwitchSite($context->remoteSiteId());

        $products = $this->findProductsByNameOrSku($searchQuery, $excludePostId);

        $this->maybeRestoreSite($previousSite);

        return array_map([$this, 'formatProducts'], $products);
    }

    /**
     * @param \stdClass $product
     * @return array
     */
    private function formatProducts(\stdClass $product): array
    {
        return [
            'id' => (int)$product->ID,
            'title' => $product->post_title,
        ];
    }

    /**
     * @param string $searchQuery
     * @param int $excludePostId
     * @return array|object|null
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     * phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
     */
    protected function findProductsByNameOrSku(string $searchQuery, int $excludePostId)
    {
        $sql = <<<SQL
SELECT {$this->wpdb->posts}.ID, {$this->wpdb->posts}.post_title FROM {$this->wpdb->posts}
INNER JOIN {$this->wpdb->postmeta} ON ( {$this->wpdb->posts}.ID = {$this->wpdb->postmeta}.post_id )
WHERE {$this->wpdb->posts}.post_type = 'product'
AND ((({$this->wpdb->posts}.post_title LIKE %s) OR ({$this->wpdb->posts}.post_excerpt LIKE %s) OR ({$this->wpdb->posts}.post_content LIKE %s)))
AND ({$this->wpdb->posts}.post_status IN ('publish', 'future', 'pending', 'private'))
AND ({$this->wpdb->posts}.ID NOT IN (%d))
OR (({$this->wpdb->postmeta}.meta_key = '_sku' AND {$this->wpdb->postmeta}.meta_value LIKE %s))
GROUP BY {$this->wpdb->posts}.ID
ORDER BY {$this->wpdb->posts}.post_date
DESC LIMIT 0, %d
SQL;
        //phpcs:enable

        $escapedLikeQuery = '%' . $this->wpdb->esc_like($searchQuery) . '%';

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                $sql,
                $escapedLikeQuery,
                $escapedLikeQuery,
                $escapedLikeQuery,
                $excludePostId,
                $escapedLikeQuery,
                apply_filters(self::FILTER_PRODUCT_SEARCH_LIMIT, resolve('product_search_limit'))
            )
        );
        //phpcs:enable
    }
}
