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
 * Class OverrideCrossellsProducts
 */
final class OverrideCrossellsProducts
{
    /**
     * @inheritdoc
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        $key = MetaboxFields::FIELD_CROSSELLS_PRODUCTS;
        $label = _x('Cross-sell Products', 'WooCommerce Product Field', 'multilingualpress');

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <tr class="show_if_variable show_if_simple">
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
                        "Override the translation's cross-sell products with the cross-sell products of this one.",
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
