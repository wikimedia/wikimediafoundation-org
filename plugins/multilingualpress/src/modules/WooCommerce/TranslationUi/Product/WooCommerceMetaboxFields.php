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

use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field\ProductUrlButtonText;
use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field\RegularPrice;
use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field\SalePrice;
use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field\PurchaseNote;
use Inpsyde\MultilingualPress\TranslationUi\Post;
use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field\ProductUrl;

/**
 * MultilingualPress Metabox Fields for WooCommerce Panel
 */
class WooCommerceMetaboxFields
{
    /**
     * Build the WooCommerce General metabox fields
     *
     * @return array
     */
    public function generalSettingFields(): array
    {
        return [
            new MetaboxField(
                new Post\MetaboxField(
                    MetaboxFields::FIELD_PRODUCT_URL,
                    new ProductUrl(),
                    'esc_url_raw'
                )
            ),
            new MetaboxField(
                new Post\MetaboxField(
                    MetaboxFields::FIELD_PRODUCT_URL_BUTTON_TEXT,
                    new ProductUrlButtonText(),
                    'esc_attr'
                )
            ),
            new MetaboxField(
                new Post\MetaboxField(
                    MetaboxFields::FIELD_REGULAR_PRICE,
                    new RegularPrice(),
                    'esc_attr'
                )
            ),
            new MetaboxField(
                new Post\MetaboxField(
                    MetaboxFields::FIELD_SALE_PRICE,
                    new SalePrice(),
                    'esc_attr'
                )
            ),
        ];
    }

    /**
     * Build the WooCommerce Invetory metabox fields
     *
     * @return array
     */
    public function inventorySettingFields(): array
    {
        return [
            new MetaboxField(
                new Post\MetaboxField(
                    MetaboxFields::FIELD_SKU,
                    new Field\Sku()
                )
            ),
        ];
    }

    /**
     * Build the WooCommerce Advanced metabox fields
     *
     * @return array
     */
    public function advancedSettingFields(): array
    {
        return [
            new MetaboxField(
                new Post\MetaboxField(
                    MetaboxFields::FIELD_PURCHASE_NOTE,
                    new PurchaseNote(),
                    'wp_kses_post'
                )
            ),
        ];
    }
}
