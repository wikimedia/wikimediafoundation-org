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
 * MultilingualPress override WooCommerce product short description
 */
class ShortDescription
{
    /**
     * @inheritdoc
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        $key = MetaboxFields::FIELD_PRODUCT_SHORT_DESCRIPTION;
        $value = $this->value($context);

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <tr>
            <th scope="row">
                <?= esc_html_x(
                    'Product Short Description',
                    'WooCommerce Product Field',
                    'multilingualpress'
                ) ?>
            </th>
            <td class="form-field <?= $key ?>_field">
                <?php
                wp_editor(
                    wp_kses_post($value),
                    $helper->fieldId($key),
                    [
                        'textarea_name' => $helper->fieldName($key),
                    ]
                );
                ?>
            </td>
        </tr>
        <?php
        $this->printDefaultEditorScripts();
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

        if (method_exists($product, 'get_short_description')) {
            $value = $product->get_short_description();
        }

        return $value;
    }

    /**
     * Print the default editor scripts to the page to be able to reinitialize the wp editor
     * after a relationship event occur.
     *
     * @see \Inpsyde\MultilingualPress\TranslationUi\Post\Ajax\RelationshipUpdater::handle()
     */
    private function printDefaultEditorScripts()
    {
        add_action('admin_print_footer_scripts', static function () {
            \_WP_Editors::print_default_editor_scripts();
        });
    }
}
