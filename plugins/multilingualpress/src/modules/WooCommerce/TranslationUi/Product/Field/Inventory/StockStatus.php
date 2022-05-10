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
class StockStatus implements RenderCallback
{
    /**
     * @var array
     */
    private $stockStatusOptions;

    /**
     * StockStatus constructor.
     *
     * @param array $stockStatusOptions A map of Woo Stock Status field options
     */
    public function __construct(array $stockStatusOptions)
    {
        $this->stockStatusOptions = $stockStatusOptions;
    }

    /**
     * Render the Stock Status Field.
     *
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     * @return void
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext)
    {
        $key = MetaboxFields::FIELD_STOCK_STATUS;
        $value = $this->value($relationshipContext);
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <div class="options_group">
            <p class="form-field mlp_<?= esc_attr($key) ?>_field">
                <label for="<?= esc_attr($helper->fieldId($key)) ?>">
                    <?= esc_html_x('Stock status', 'WooCommerce Field', 'multilingualpress') ?>
                </label>
                <select
                    class="select short"
                    name="<?= esc_attr($helper->fieldName($key)) ?>"
                    id="<?= esc_attr($helper->fieldId($key)) ?>"
                >
                    <?php foreach ($this->stockStatusOptions as $optionValue => $optionLabel) {?>
                        <option value="<?= esc_attr($optionValue) ?>" <?php selected($optionValue, $value);?>><?= esc_html($optionLabel) ?></option>
                    <?php }?>
                </select>
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
            'Controls whether or not the product is listed as "in stock" or "out of stock" on the frontend.',
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
     * @return string
     */
    protected function value(RelationshipContext $relationshipContext): string
    {
        $product = wc_get_product($relationshipContext->remotePostId());

        if (!$product || !method_exists($product, 'get_stock_status')) {
            return '';
        }

        return $product->get_stock_status() ?? '';
    }
}
