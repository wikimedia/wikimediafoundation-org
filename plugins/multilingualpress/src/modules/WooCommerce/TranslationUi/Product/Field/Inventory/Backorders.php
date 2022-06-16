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
class Backorders implements RenderCallback
{
    /**
     * @var array
     */
    private $backorderOptions;

    /**
     * Backorders constructor.
     *
     * @param array $backorderOptions A map of Woo backorder field options
     */
    public function __construct(array $backorderOptions)
    {
        $this->backorderOptions = $backorderOptions;
    }

    /**
     * Render the Manage Backorders Field.
     *
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     * @return void
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext)
    {
        $key = MetaboxFields::FIELD_BACKORDERS;
        $value = $this->value($relationshipContext);
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <div class="options_group">
            <p class="form-field mlp_<?= esc_attr($key) ?>_field">
                <label for="<?= esc_attr($helper->fieldId($key)) ?>">
                    <?= esc_html_x('Allow backorders?', 'WooCommerce Field', 'multilingualpress') ?>
                </label>
                <select
                    class="select short"
                    name="<?= esc_attr($helper->fieldName($key)) ?>"
                    id="<?= esc_attr($helper->fieldId($key)) ?>"
                >
                    <?php foreach ($this->backorderOptions as $optionValue => $optionLabel) {?>
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
            'If managing stock, this controls whether or not backorders are allowed. If enabled, stock quantity can go below 0.',
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

        if (!$product || !method_exists($product, 'get_backorders')) {
            return '';
        }

        return $product->get_backorders() ?? '';
    }
}
