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

namespace Inpsyde\MultilingualPress\TranslationUi\Term\Field;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext;
use Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields;

class ParentTerm
{

    /**
     * @param $value
     * @return int
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public static function sanitize($value): int
    {
        // phpcs:enable

        return (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $context
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        $hasRemoteTerm = $context->hasRemoteTerm();
        $term = $hasRemoteTerm ? $context->remoteTerm() : $context->sourceTerm();
        if (!is_taxonomy_hierarchical($term->taxonomy)) {
            return;
        }

        $taxonomy = get_taxonomy($term->taxonomy);
        $label = $taxonomy->labels->parent_item_colon;
        if (!$hasRemoteTerm) {
            // translators: %s is the parent item label, e.g. "Parent Category:"
            $label = _x('New %s', 'new parent item', 'multilingualpress');
        }

        $id = $helper->fieldId(MetaboxFields::FIELD_PARENT);
        ?>
        <tr>
            <th scope="row">
                <label for="<?= esc_attr($id) ?>">
                    <?= esc_html(sprintf($label, $taxonomy->labels->parent_item_colon)) ?>
                </label>
            </th>
            <td>
                <?php
                wp_dropdown_categories(
                    [
                        'hide_empty' => 0,
                        'hide_if_empty' => false,
                        'taxonomy' => $term->taxonomy,
                        'name' => $helper->fieldName(MetaboxFields::FIELD_PARENT),
                        'id' => $id,
                        'orderby' => 'name',
                        'selected' => $hasRemoteTerm ? (int)$term->parent : 0,
                        'exclude_tree' => $hasRemoteTerm ? (int)$term->term_id : '',
                        'hierarchical' => true,
                        'show_option_none' => __('-- No parent --', 'multilingualpress'),
                        'show_option_all' => __('Synchronize with source term', 'multilingualpress'),
                    ]
                );
                ?>
            </td>
        </tr>
        <?php
    }
}
