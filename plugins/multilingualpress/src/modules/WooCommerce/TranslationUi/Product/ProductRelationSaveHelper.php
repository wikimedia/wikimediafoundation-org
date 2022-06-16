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

namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product;

use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
use Inpsyde\MultilingualPress\TranslationUi\Post;
use Inpsyde\MultilingualPress\TranslationUi\Term;

/**
 * Class ProductRelationSaveHelper
 */
class ProductRelationSaveHelper
{
    use SwitchSiteTrait;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ContentRelations
     */
    private $contentRelations;

    /**
     * ProductRelationSaveHelper constructor
     *
     * @param Request $request
     * @param ContentRelations $contentRelations
     */
    public function __construct(Request $request, ContentRelations $contentRelations)
    {
        $this->request = $request;
        $this->contentRelations = $contentRelations;
    }

    /**
     * @param array $attributes
     * @return array
     */
    public function filterProductCustomAttributes(array $attributes): array
    {
        return array_filter(
            $attributes,
            static function (string $attributeName): bool {
                return strpos($attributeName, 'pa_') !== 0;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @param array $attributes
     * @return array
     */
    public function filterProductAttributesTerms(array $attributes): array
    {
        return array_filter(
            $attributes,
            static function (string $attributeName): bool {
                return strpos($attributeName, 'pa_') === 0;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Retrieve related remote terms and create them in the remote site if necessary.
     *
     * @param array $sourceTermsIds
     * @param int $sourceSiteId
     * @param int $remoteSiteId
     * @param string $taxonomyName
     * @return array
     *
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    public function mayRelateTerms(
        array $sourceTermsIds,
        int $sourceSiteId,
        int $remoteSiteId,
        string $taxonomyName
    ): array {

        // phpcs:enable

        $attributesTermsIds = [];
        $sourceTerms = [];

        $this->maybeSwitchSite($sourceSiteId);
        foreach ($sourceTermsIds as $sourceTermId) {
            $sourceTerms[] = get_term($sourceTermId, $taxonomyName);
        }
        $this->maybeRestoreSite($remoteSiteId);

        foreach ($sourceTerms as $sourceTerm) {
            if (!$sourceTerm instanceof \WP_Term) {
                continue;
            }

            $remoteTermId = $this->contentRelations->contentIdForSite(
                $sourceSiteId,
                $sourceTerm->term_id,
                'term',
                $remoteSiteId
            );

            $remoteTerm = $this->maybeTerm($remoteTermId, $sourceTerm->name, $taxonomyName);
            if (!$remoteTerm) {
                continue;
            }

            if (!$remoteTermId) {
                $this->relateTerms(
                    $sourceSiteId,
                    $sourceTerm->term_id,
                    $remoteSiteId,
                    $remoteTerm->term_id
                );
            }

            $attributesTermsIds[] = $remoteTerm->term_id;
        }

        return $attributesTermsIds;
    }

    /**
     * Get the remote product based on the product type value given in the current request
     *
     * @param int $sourceSiteId
     * @param int $sourceProductId
     * @param int $remoteSiteId
     * @param bool $overrideProductType
     * @return \WC_Product
     * @throws \DomainException
     */
    public function remoteProduct(
        int $sourceSiteId,
        int $sourceProductId,
        int $remoteSiteId,
        bool $overrideProductType
    ): \WC_Product {

        $remoteProductId = $this->contentRelations->contentIdForSite(
            $sourceSiteId,
            $sourceProductId,
            'post',
            $remoteSiteId
        );

        $productType = (string)$this->request->bodyValue(
            MetaboxAction::PRODUCT_TYPE_FIELD_NAME,
            INPUT_POST,
            FILTER_SANITIZE_STRING
        );

        $productType = $productType && $overrideProductType
            ? sanitize_title($productType)
            : \WC_Product_Factory::get_product_type($remoteProductId);

        $className = \WC_Product_Factory::get_product_classname(
            $remoteProductId,
            $productType ? $productType : MetaboxAction::DEFAULT_PRODUCT_TYPE
        );

        $product = new $className($remoteProductId);

        return $product;
    }

    /**
     * Retrieve the related products by a specific remote site
     *
     * @param int $sourceSiteId
     * @param int $remoteSiteId
     * @param array $productsIds
     * @return array
     */
    public function relatedProductsForSiteId(
        int $sourceSiteId,
        int $remoteSiteId,
        array $productsIds
    ): array {

        $relatedProducts = [];

        foreach ($productsIds as $productId) {
            $relations = $this->contentRelations->relations(
                $sourceSiteId,
                (int)$productId,
                'post'
            );

            if (!$relations) {
                continue;
            }

            $relatedProducts[] = $relations[$remoteSiteId];
        }

        return $relatedProducts;
    }

    /**
     * @param Post\RelationshipContext $context
     * @param \WC_Product_Variation $sourceVariation
     * @return array
     */
    public function relatedAttributeTerms(
        Post\RelationshipContext $context,
        \WC_Product_Variation $sourceVariation
    ): array {

        $siteId = get_current_blog_id();
        $sourceSiteId = $context->sourceSiteId();
        $sourceAttributesTerms = $this->filterProductAttributesTerms(
            $sourceVariation->get_attributes()
        );
        $remoteAttributeTerms = [];

        $this->maybeSwitchSite($sourceSiteId);
        foreach ($sourceAttributesTerms as $taxonomyName => $sourceAttributeTermSlug) {
            $term = get_term_by('slug', $sourceAttributeTermSlug, $taxonomyName);
            if (!$term) {
                continue;
            }

            $remoteTermId = $this->contentRelations->contentIdForSite(
                $sourceSiteId,
                $term->term_id,
                'term',
                $context->remoteSiteId()
            );

            if (!$remoteTermId) {
                continue;
            }

            $remoteAttributeTerms[$taxonomyName] = $remoteTermId;
        }
        $this->maybeRestoreSite($siteId);

        foreach ($remoteAttributeTerms as $taxonomyName => $remoteAttributeTermId) {
            $term = get_term($remoteAttributeTermId, $taxonomyName);
            if ($term instanceof \WP_Term) {
                $remoteAttributeTerms[$taxonomyName] = $term->slug;
            }
        }

        return $remoteAttributeTerms;
    }

    /**
     * Relate terms between source and remote site
     *
     * @param int $sourceSiteId
     * @param int $sourceTermId
     * @param int $remoteSiteId
     * @param int $remoteTermId
     */
    protected function relateTerms(
        int $sourceSiteId,
        int $sourceTermId,
        int $remoteSiteId,
        int $remoteTermId
    ) {

        $termHelper = new Term\TermRelationSaveHelper($this->contentRelations);
        $relationshipContext = new Term\RelationshipContext(
            [
                Term\RelationshipContext::SOURCE_SITE_ID => $sourceSiteId,
                Term\RelationshipContext::SOURCE_TERM_ID => $sourceTermId,
                Term\RelationshipContext::REMOTE_SITE_ID => $remoteSiteId,
                Term\RelationshipContext::REMOTE_TERM_ID => $remoteTermId,
            ]
        );
        $termHelper->relateTerms($relationshipContext);
    }

    /**
     * Create a term if not exists in the current site.
     *
     * @param int $remoteTermId
     * @param string $sourceTermName
     * @param string $taxonomyName
     * @return mixed
     *
     * @phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    protected function maybeTerm(int $remoteTermId, string $sourceTermName, string $taxonomyName)
    {
        // phpcs:enable

        $newTermId = $remoteTermId;

        $remoteTerm = term_exists($sourceTermName, $taxonomyName);
        if (!empty($remoteTerm['term_id'])) {
            return get_term_by('id', $remoteTerm['term_id'], $taxonomyName);
        }

        $taxonomy = get_taxonomy($taxonomyName);
        if (!term_exists($remoteTermId) && current_user_can($taxonomy->cap->edit_terms)) {
            $newTermId = wp_insert_term($sourceTermName, $taxonomyName);
        }
        if (!$newTermId || is_wp_error($newTermId)) {
            return false;
        }

        if (is_array($newTermId)) {
            $newTermId = $newTermId['term_id'];
        }

        return get_term($newTermId, $taxonomyName);
    }
}
