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
 * MultilingualPress Product Url Button Text Field
 */
class ProductUrlButtonText
{
    /**
     * Render the Product Url Button Text Field.
     *
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     * @return mixed|void
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext)
    {
        $key = MetaboxFields::FIELD_PRODUCT_URL_BUTTON_TEXT;
        $value = $this->value($relationshipContext);
        $placeholder = _x(
            'Buy Product',
            'WooCommerce Product Field Placeholder',
            'multilingualpress'
        );

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <div class="options_group show_if_external">
            <p class="form-field <?= $key ?>_field">
                <label for="<?= esc_attr($helper->fieldId($key)) ?>">
                    <?= esc_html_x(
                        'Button Text',
                        'WooCommerce Product Field',
                        'multilingualpress'
                    ) ?>
                </label>
                <input
                    type="text"
                    class="short"
                    name="<?= esc_attr($helper->fieldName($key)) ?>"
                    id="<?= esc_attr($helper->fieldId($key)) ?>"
                    value="<?= esc_attr($value) ?>"
                    placeholder="<?= esc_attr($placeholder) ?>"
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
    private function descriptionTooltip(): string
    {
        $description = esc_html_x(
            'This text will be shown on the button linking to the external product.',
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
    private function value(RelationshipContext $relationshipContext): string
    {
        /** @var \WC_Product_External $product */
        $product = wc_get_product($relationshipContext->remotePostId());
        $value = '';

        if (!$product) {
            return $value;
        }

        if (method_exists($product, 'get_button_text')) {
            $value = $product->get_button_text('edit');
        }

        return $value;
    }
}
