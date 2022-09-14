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

namespace Inpsyde\MultilingualPress\Module\WooCommerce;

use Inpsyde\MultilingualPress\Core\TaxonomyRepository;
use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\NetworkState;

/**
 * Class AttributesRelationship
 */
class AttributesRelationship
{
    const WC_ATTRIBUTE_TAXONOMY_PREFIX = 'pa_';

    /**
     * @var \wpdb
     */
    private $wpdb;

    /**
     * @var SiteRelations
     */
    private $siteRelations;

    /**
     * @var TaxonomyRepository
     */
    private $taxonomyRepository;

    /**
     * AttributesRelationship constructor
     *
     * @param TaxonomyRepository $taxonomyRepository
     * @param SiteRelations $siteRelations
     * @param \wpdb $wpdb
     */
    public function __construct(
        TaxonomyRepository $taxonomyRepository,
        SiteRelations $siteRelations,
        \wpdb $wpdb
    ) {

        $this->taxonomyRepository = $taxonomyRepository;
        $this->siteRelations = $siteRelations;
        $this->wpdb = $wpdb;
    }

    /**
     * Create attribute taxonomy into current site by getting data by the source site
     *
     * @param \WP_Term $term
     * @param string $taxonomy
     * @return void
     */
    public function createAttributeRelation(\WP_Term $term, string $taxonomy)
    {
        $sourceSiteId = get_current_blog_id();

        if (
            substr($taxonomy, 0, 3) !== self::WC_ATTRIBUTE_TAXONOMY_PREFIX
            || !taxonomy_exists($taxonomy)
        ) {
            return;
        }

        $siteIds = $this->siteRelations->relatedSiteIds($sourceSiteId);
        if (!$siteIds) {
            return;
        }

        $attributeName = substr($taxonomy, 3);
        try {
            $sourceAttribute = $this->sourceAttributesByName(
                $sourceSiteId,
                $attributeName
            );
        } catch (\Exception $exc) {
            return;
        }

        $networkState = NetworkState::create();
        foreach ($siteIds as $siteId) {
            switch_to_blog($siteId);

            $remoteAttribute = $this->sourceAttributesByName($siteId, $attributeName);
            if ($remoteAttribute) {
                $this->addSupportForAttribute(
                    (int)$remoteAttribute['attribute_id'],
                    $sourceAttribute
                );
                continue;
            }

            $inserted = $this->insertAttributeTaxonomy($sourceAttribute);
            if (!$inserted) {
                continue;
            }

            $this->addSupportForAttribute($inserted, $sourceAttribute);

            wp_schedule_single_event(time(), 'woocommerce_flush_rewrite_rules');
            delete_transient('wc_attribute_taxonomies');
        }
        $networkState->restore();
    }

    /**
     * Add translation support for attribute taxonomy
     *
     * @param int $id
     * @param array $data
     */
    public function addSupportForAttribute(int $id, array $data)
    {
        $taxonomies = $this->translatableTaxonomies();

        $taxonomies = array_merge($taxonomies, [
            wc_attribute_taxonomy_name($data['attribute_name']) => [
                TaxonomyRepository::FIELD_ACTIVE => true,
                TaxonomyRepository::FIELD_SKIN => '',
            ],
        ]);

        $this->taxonomyRepository->supportTaxonomies($taxonomies);
    }

    /**
     * Retrieve data for the attribute
     *
     * @param int $siteId
     * @param string $attributeName
     * @return array
     */
    private function sourceAttributesByName(
        int $siteId,
        string $attributeName
    ): array {

        $sourceDbPrefix = $this->wpdb->get_blog_prefix($siteId);

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        //phpcs:disable WordPress.DB.PreparedSQLPlaceholders.QuotedSimplePlaceholder
        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $sourceAttribute = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$sourceDbPrefix}woocommerce_attribute_taxonomies WHERE attribute_name='%s'",
                $attributeName
            ),
            ARRAY_A
        );
        //phpcs:enable

        if (!is_array($sourceAttribute)) {
            return [];
        }

        return $sourceAttribute;
    }

    /**
     * Retrieve all of the translatable taxonomies even the ones not active
     *
     * @return array
     */
    private function translatableTaxonomies(): array
    {
        return get_network_option(0, TaxonomyRepository::OPTION, []);
    }

    /**
     * Insert attribute taxonomy into db
     *
     * @param array $attribute
     * @return int
     */
    private function insertAttributeTaxonomy(array $attribute): int
    {
        $label = $attribute['attribute_label'] ?? '';
        $name = $attribute['attribute_name'] ?? sanitize_title($label);

        if (!$name || !$label) {
            return 0;
        }

        $attribute = wp_parse_args(
            $attribute,
            [
                'attribute_label' => $label,
                'attribute_name' => $name,
                'attribute_type' => 'select',
                'attribute_orderby' => 'menu_order',
                'attribute_public' => '0',
            ]
        );
        unset($attribute['attribute_id']);

        return (int)$this->wpdb->insert(
            $this->wpdb->prefix . 'woocommerce_attribute_taxonomies',
            $attribute,
            ['%s', '%s', '%s', '%s', '%d']
        );
    }
}
