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

namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field;

use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

/**
 * MultilingualPress Product Sale Price Field
 */
class SalePrice
{
    /**
     * Render the Product Sale Price Field.
     *
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     * @return mixed|void
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext)
    {
        $key = MetaboxFields::FIELD_SALE_PRICE;
        $value = $this->value($relationshipContext);

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <div class="options_group show_if_simple show_if_external">
            <p class="form-field <?= $key ?>_field">
                <label for="<?= esc_attr($helper->fieldId($key)) ?>">
                    <?= esc_html_x(
                        'Sale Price',
                        'WooCommerce Product Field',
                        'multilingualpress'
                    ) ?>
                </label>
                <input
                    type="text"
                    class="wc_input_price"
                    name="<?= esc_attr($helper->fieldName($key)) ?>"
                    id="<?= esc_attr($helper->fieldId($key)) ?>"
                    value="<?= esc_attr($value) ?>"
                />
                <?= wp_kses(
                    $this->salePriceTooltip(),
                    [
                        'span' => [
                            'class' => true,
                            'data-tip' => true,
                        ],
                    ]
                ); ?>
            </p>
        </div>
        <?php
        // phpcs:enabled
    }

    /**
     * Retrieve the value for the input field.
     *
     * @param RelationshipContext $relationshipContext
     * @return string
     */
    private function value(RelationshipContext $relationshipContext): string
    {
        $product = wc_get_product($relationshipContext->remotePostId());
        $value = '';

        if (!$product) {
            return $value;
        }

        if (method_exists($product, 'get_sale_price')) {
            $value = $product->get_sale_price();
        }

        switch_to_blog($relationshipContext->sourceSiteId());
        $decimals = wc_get_price_decimal_separator();
        restore_current_blog();

        return (string)preg_replace('/\D/', $decimals, $value);
    }

    /**
     * Build Sale Price ToolTip
     *
     * @return string
     */
    private function salePriceTooltip(): string
    {
        $description = _x(
            'The Decimal and Thousand separators will be automatically converted in regards of remote site options',
            'Product data sale price translation meta box',
            'multilingualpress'
        );

        return wc_help_tip($description);
    }
}
