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
 * MultilingualPress Purchase Note Field
 */
class PurchaseNote
{
    /**
     * Render the Purchase Note Field.
     *
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     * @return mixed|void
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext)
    {
        $key = MetaboxFields::FIELD_PURCHASE_NOTE;
        $value = $this->value($relationshipContext);

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <div class="options_group">
            <p class="form-field <?= $key ?>_field">
                <label for="<?= esc_attr($helper->fieldId($key)) ?>">
                    <?= esc_html_x(
                        'Purchase Note',
                        'WooCommerce Product Field',
                        'multilingualpress'
                    ) ?>
                </label>
                <textarea
                    name="<?= esc_attr($helper->fieldName($key)) ?>"
                    id="<?= esc_attr($helper->fieldId($key)) ?>"
                    rows="3"
                    class="large-text"><?= wp_kses_post($value) ?></textarea>
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
        $description = _x(
            'Enter an optional note to send the customer after purchase.',
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
        $product = wc_get_product($relationshipContext->remotePostId());
        $value = '';

        if (!$product) {
            return $value;
        }

        if (method_exists($product, 'get_purchase_note')) {
            $value = $product->get_purchase_note('edit');
        }

        return $value;
    }
}
