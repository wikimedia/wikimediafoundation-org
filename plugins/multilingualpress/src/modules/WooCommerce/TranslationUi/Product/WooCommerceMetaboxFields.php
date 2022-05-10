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

use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field\ProductUrl;
use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field\ProductUrlButtonText;
use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field\PurchaseNote;
use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field\RegularPrice;
use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field\SalePrice;
use Inpsyde\MultilingualPress\TranslationUi\Post;

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
                    new Field\Inventory\Sku()
                )
            ),
            new MetaboxField(
                new Post\MetaboxField(
                    MetaboxFields::FIELD_MANAGE_STOCK,
                    new Field\Inventory\ManageStock()
                )
            ),
            new MetaboxField(
                new Post\MetaboxField(
                    MetaboxFields::FIELD_STOCK,
                    new Field\Inventory\Stock()
                )
            ),
            new MetaboxField(
                new Post\MetaboxField(
                    MetaboxFields::FIELD_BACKORDERS,
                    new Field\Inventory\Backorders(wc_get_product_backorder_options())
                )
            ),
            new MetaboxField(
                new Post\MetaboxField(
                    MetaboxFields::FIELD_LOW_STOCK_AMOUNT,
                    new Field\Inventory\LowStockAmount((int)get_option('woocommerce_notify_low_stock_amount'))
                )
            ),
            new MetaboxField(
                new Post\MetaboxField(
                    MetaboxFields::FIELD_STOCK_STATUS,
                    new Field\Inventory\StockStatus(wc_get_product_stock_status_options())
                )
            ),
            new MetaboxField(
                new Post\MetaboxField(
                    MetaboxFields::FIELD_SOLD_INDIVIDUALLY,
                    new Field\Inventory\SoldIndividually()
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
