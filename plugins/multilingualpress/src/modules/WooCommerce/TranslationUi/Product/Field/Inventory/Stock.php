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

namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\Field\Inventory;

use Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product\MetaboxFields;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;
use Inpsyde\MultilingualPress\TranslationUi\Post\RenderCallback;

/**
 * MultilingualPress Product Inventory Field
 */
class Stock implements RenderCallback
{
    /**
     * Render the Stock Field.
     *
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     * @return void
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext)
    {
        $key = MetaboxFields::FIELD_STOCK;
        $value = $this->value($relationshipContext);
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <div class="options_group">
            <p class="form-field mlp_<?= esc_attr($key) ?>_field">
                <label for="<?= esc_attr($helper->fieldId($key)) ?>">
                    <?= esc_html_x('Stock quantity', 'WooCommerce Field', 'multilingualpress') ?>
                </label>
                <input
                    type="number"
                    class="short wc_input_stock"
                    step="any"
                    name="<?= esc_attr($helper->fieldName($key)) ?>"
                    id="<?= esc_attr($helper->fieldId($key)) ?>"
                    value="<?= esc_attr($value) ?>"
                />
                <?= $this->descriptionTooltip() ?>
            </p>
        </div>
        <?php
        // phpcs:enabled
    }

    /**
     * Build Description ToolTip
     *
     * @return string
     */
    protected function descriptionTooltip(): string
    {
        $description = _x(
            'Stock quantity. If this is a variable product this value will be used to control stock for all variations,
            unless you define stock at variation level.',
            'WooCommerce Product Field',
            'multilingualpress'
        );

        return wp_kses(
            wc_help_tip($description),
            [
                'span' => [
                    'class' => true,
                    'data-tip' => true,
                ],
            ]
        );
    }

    /**
     * Retrieve the value for the input field.
     *
     * @param RelationshipContext $relationshipContext
     * @return int
     */
    protected function value(RelationshipContext $relationshipContext): int
    {
        $product = wc_get_product($relationshipContext->remotePostId());

        if (!$product || !method_exists($product, 'get_stock_quantity')) {
            return 0;
        }

        return $product->get_stock_quantity() ?? 0;
    }
}
