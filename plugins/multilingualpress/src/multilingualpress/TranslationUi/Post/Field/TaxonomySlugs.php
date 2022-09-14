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

namespace Inpsyde\MultilingualPress\TranslationUi\Post\Field;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;
use Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields;

class TaxonomySlugs
{
    const FILTER_FIELD_TAXONOMY_SLUGS = 'multilingualpress.field_taxonomy_slugs';

    /**
     * @param $value
     * @return int[]
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public static function sanitize($value): array
    {
        // phpcs:enable
        if (!is_array($value)) {
            return [];
        }

        $value = filter_var($value, FILTER_SANITIZE_STRING, ['flags' => FILTER_FORCE_ARRAY]);

        return array_filter($value);
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $context
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        $post = $context->sourcePost();
        $taxonomies = get_object_taxonomies($post, 'objects');
        $name = $helper->fieldName(MetaboxFields::FIELD_TAXONOMY_SLUGS);

        /**
         * Filter Field Taxonomy Slugs
         *
         * @param array $taxonomies The list of the taxonomy to use to build the field.
         */
        $taxonomies = apply_filters(self::FILTER_FIELD_TAXONOMY_SLUGS, $taxonomies);

        /** @var \WP_Taxonomy $taxonomy */
        foreach ($taxonomies as $slug => $taxonomy) {
            if (
                $taxonomy
                && $taxonomy->show_ui
                && current_user_can($taxonomy->cap->assign_terms, $slug)
            ) {
                ?>
                <input
                    type="hidden"
                    data-slug=<?= esc_attr($slug) ?>
                    name="<?= esc_attr($name) ?>[]"
                    value="">
                <?php
            }
        }
    }
}
