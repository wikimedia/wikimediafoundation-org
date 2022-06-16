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
class SoldIndividually implements RenderCallback
{
    /**
     * Render the Sold Individually Field.
     *
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     * @return void
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext)
    {
        $key = MetaboxFields::FIELD_SOLD_INDIVIDUALLY;
        $value = $this->value($relationshipContext);
        ?>
        <div class="options_group">
            <p class="form-field mlp_<?= esc_attr($key) ?>_field">
                <label for="<?= esc_attr($helper->fieldId($key)) ?>">
                    <?= esc_html_x('Sold individually', 'WooCommerce Field', 'multilingualpress') ?>
                </label>
                <input
                    type="checkbox"
                    class="checkbox"
                    name="<?= esc_attr($helper->fieldName($key)) ?>"
                    id="<?= esc_attr($helper->fieldId($key)) ?>"
                    <?php checked($value, true);?>
                />
                <span class="description"><?= esc_html($this->description()) ?></span>
            </p>
        </div>
        <?php
    }

    /**
     * Build Description Message
     *
     * @return string
     */
    private function description(): string
    {
        return _x(
            'Enable this to only allow one of this item to be bought in a single order',
            'WooCommerce Product Field',
            'multilingualpress'
        );
    }

    /**
     * Retrieve the value for the input field.
     *
     * @param RelationshipContext $relationshipContext
     * @return bool
     */
    private function value(RelationshipContext $relationshipContext): bool
    {
        $product = wc_get_product($relationshipContext->remotePostId());

        if (!$product || !method_exists($product, 'get_sold_individually')) {
            return false;
        }

        return $product->get_sold_individually();
    }
}
