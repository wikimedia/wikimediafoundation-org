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
class LowStockAmount implements RenderCallback
{
    /**
     * @var int
     */
    private $lowStockAmount;

    /**
     * LowStockAmount constructor.
     *
     * @param int $lowStockAmount Woo Store-wide threshold amount value
     */
    public function __construct(int $lowStockAmount)
    {
        $this->lowStockAmount = $lowStockAmount;
    }

    /**
     * Render the Manage Low Stock Amount Field.
     *
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     * @return void
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext)
    {
        $key = MetaboxFields::FIELD_LOW_STOCK_AMOUNT;
        $value = $this->value($relationshipContext);
        $lowStockAmount = get_option('woocommerce_notify_low_stock_amount') ?? 0;
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <div class="options_group">
            <p class="form-field mlp_<?= esc_attr($key) ?>_field">
                <label for="<?= esc_attr($helper->fieldId($key)) ?>">
                    <?= esc_html_x('Low stock threshold', 'WooCommerce Field', 'multilingualpress') ?>
                </label>
                <input
                    type="number"
                    class="short"
                    placeholder="<?= esc_attr($this->placeholder())?>"
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
            'When product stock reaches this amount you will be notified by email.
             It is possible to define different values for each variation individually.
             The shop default value can be set in Settings > Products > Inventory.',
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
     * Create the placeholder text for Low Stock Amount field
     *
     * @return string
     */
    protected function placeholder(): string
    {
        return sprintf(
            /* translators: %d: Amount of stock left */
            esc_attr__('Store-wide threshold (%d)', 'multilingualpress'),
            esc_attr($this->lowStockAmount)
        );
    }

    /**
     * Retrieve the value for the input field.
     *
     * @param RelationshipContext $relationshipContext
     * @return string
     */
    protected function value(RelationshipContext $relationshipContext): int
    {
        $product = wc_get_product($relationshipContext->remotePostId());

        if (!$product || !method_exists($product, 'get_low_stock_amount')) {
            return 0;
        }

        return (int)$product->get_low_stock_amount() ?? 0;
    }
}
