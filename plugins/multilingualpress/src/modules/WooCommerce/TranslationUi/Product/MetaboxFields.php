<?php # -*- coding: utf-8 -*-
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

use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field;
use Inpsyde\MultilingualPress\TranslationUi\Post;

/**
 * MultilingualPress WooCommerce Metabox Fields
 */
class MetaboxFields
{
    const TAB = 'tab-product';

    const FIELD_PRODUCT_URL = 'product_url';
    const FIELD_PRODUCT_URL_BUTTON_TEXT = 'button_text';
    const FIELD_OVERRIDE_PRODUCT_TYPE = 'override_product_type';
    const FIELD_OVERRIDE_PRODUCT_GALLERY = 'override_product_gallery';
    const FIELD_OVERRIDE_VARIATIONS = 'override_attribute_variations';
    const FIELD_OVERRIDE_ATTRIBUTES = 'override_attributes';
    const FIELD_OVERRIDE_DOWNLOADABLE_FILES = 'override_downloadable_files';
    const FIELD_REGULAR_PRICE = 'regular_price';
    const FIELD_SALE_PRICE = 'sale_price';
    const FIELD_PRODUCT_SHORT_DESCRIPTION = 'product_short_description';
    const FIELD_PURCHASE_NOTE = 'purchase_note';
    const FIELD_SKU = 'sku';
    const FIELD_GROUPED_PRODUCTS = 'grouped_products';
    const FIELD_CROSSELLS_PRODUCTS = 'crossells_products';
    const FIELD_UPSELLS_PRODUCTS = 'upsells_products';

    /**
     * @var WooCommerceMetaboxFields
     */
    private $wooCommerceFields;

    /**
     * MetaboxFields constructor.
     * @param WooCommerceMetaboxFields $wooCommerceFields
     */
    public function __construct(WooCommerceMetaboxFields $wooCommerceFields)
    {
        $this->wooCommerceFields = $wooCommerceFields;
    }

    /**
     * Retrieve all fields for the WooCommerce metabox tab.
     *
     * @return MetaboxTab[]
     */
    public function allFieldsTabs(): array
    {
        return [
            new MetaboxTab(
                MetaboxFields::TAB,
                _x('Product Data', 'translation post metabox', 'multilingualpress'),
                new Post\MetaboxField(
                    MetaboxFields::FIELD_PRODUCT_SHORT_DESCRIPTION,
                    new Field\ShortDescription()
                ),
                new Post\MetaboxField(
                    self::FIELD_OVERRIDE_PRODUCT_TYPE,
                    new Field\OverrideProductType()
                ),
                new Post\MetaboxField(
                    self::FIELD_OVERRIDE_ATTRIBUTES,
                    new Field\OverrideAttributes()
                ),
                new Post\MetaboxField(
                    self::FIELD_OVERRIDE_VARIATIONS,
                    new Field\OverrideVariations()
                ),
                new Post\MetaboxField(
                    MetaboxFields::FIELD_OVERRIDE_DOWNLOADABLE_FILES,
                    new Field\OverrideDownloadableFiles()
                ),
                new Post\MetaboxField(
                    MetaboxFields::FIELD_OVERRIDE_PRODUCT_GALLERY,
                    new Field\OverrideProductGallery()
                ),
                new Post\MetaboxField(
                    MetaboxFields::FIELD_GROUPED_PRODUCTS,
                    new Field\OverrideGroupedProducts()
                ),
                new Post\MetaboxField(
                    MetaboxFields::FIELD_UPSELLS_PRODUCTS,
                    new Field\OverrideUpsellsProducts()
                ),
                new Post\MetaboxField(
                    MetaboxFields::FIELD_CROSSELLS_PRODUCTS,
                    new Field\OverrideCrossellsProducts()
                ),
                ...$this->wooCommerceFields->generalSettingFields(),
                ...$this->wooCommerceFields->inventorySettingFields(),
                ...$this->wooCommerceFields->advancedSettingFields()
            ),
        ];
    }
}
