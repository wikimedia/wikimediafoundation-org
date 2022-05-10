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
 * Class OverrideUpsellsProducts
 */
class OverrideUpsellsProducts
{
    /**
     * @inheritdoc
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        $key = MetaboxFields::FIELD_UPSELLS_PRODUCTS;
        $label = _x('Upsell Products', 'WooCommerce Product Field', 'multilingualpress');

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <tr>
            <th scope="row">
                <?= esc_html($label) ?>
            </th>
            <td class="form-field <?= $key ?>_field">
                <label for="<?= esc_attr($helper->fieldId($key)) ?>">
                    <input
                        type="checkbox"
                        name="<?= esc_attr($helper->fieldName($key)) ?>"
                        id="<?= esc_attr($helper->fieldId($key)) ?>"
                    />
                    <?= esc_html_x(
                        "Override the translation's upsell products with the upsell products of this one.",
                        'WooCommerce Product Field',
                        'multilingualpress'
                    ) ?>
                </label>
            </td>
        </tr>
        <?php
        // phpcs:enabled
    }
}
