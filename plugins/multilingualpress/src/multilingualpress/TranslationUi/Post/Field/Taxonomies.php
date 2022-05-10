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

class Taxonomies
{
    const FILTER_SINGLE_TERM_TAXONOMIES = 'multilingualpress.single_term_taxonomies';
    const FILTER_TRANSLATION_UI_SELECT_THRESHOLD = 'multilingualpress.translation_ui_select_threshold';
    const FILTER_TRANSLATION_UI_USE_SELECT = 'multilingualpress.translation_ui_taxonomies_use_select';

    /**
     * @var \WP_Taxonomy
     */
    private $taxonomy;

    /**
     * @var \WP_Term[]
     */
    private $terms;

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

        $sanitized = [];
        foreach ($value as $taxonomy => $ids) {
            if (!is_string($taxonomy) || !is_array($ids)) {
                continue;
            }
            $sanitized[$taxonomy] = wp_parse_id_list($ids);
        }

        return array_filter($sanitized);
    }

    /**
     * @param \WP_Taxonomy $taxonomy
     * @param \WP_Term[] ...$terms
     */
    public function __construct(\WP_Taxonomy $taxonomy, \WP_Term ...$terms)
    {
        $this->taxonomy = $taxonomy;
        $this->terms = $terms;
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $context
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        $base = MetaboxFields::FIELD_TAXONOMIES;
        $idBase = "{$base}-{$this->taxonomy->name}";
        $name = $helper->fieldName("{$base}][{$this->taxonomy->name}");
        $siteId = $context->remoteSiteId();
        $assignedTerms = get_the_terms($context->remotePost(), $this->taxonomy->name);
        $assignedIds = is_array($assignedTerms)
            ? wp_parse_id_list(array_column($assignedTerms, 'term_id'))
            : [];

        $inputType = $this->inputType($this->taxonomy->term_count);
        $isSelect = apply_filters(
            self::FILTER_TRANSLATION_UI_USE_SELECT,
            strpos($inputType, 'select') === 0,
            $this->taxonomy->name
        );

        ?>
        <tr class="mlp-taxonomy-box" data-type="<?= esc_attr($this->taxonomy->name)?>">
            <?php
            $isSelect
                ? $this->renderSelect($assignedIds, $idBase, $name, $inputType)
                : $this->renderInputs($assignedIds, $name, $inputType, $siteId);
            ?>
        </tr>
        <?php
    }

    /**
     * @param array $assignedIds
     * @param string $idBase
     * @param string $name
     * @param string $type
     */
    private function renderSelect(array $assignedIds, string $idBase, string $name, string $type)
    {
        if ($type !== 'select') {
            $type = $type === 'radio' ? 'select' : 'select multiple';
        }

        $multiple = $type === 'select multiple';
        $multiple or $assignedIds = reset($assignedIds);

        ?>
        <th scope="row">
            <?= esc_html($this->taxonomy->label) ?>
        </th>
        <td>
            <select<?= $multiple ? ' multiple' : '' ?>
                id="<?= esc_attr($idBase) ?>"
                name="<?= esc_attr($name) ?><?= $multiple ? '[]' : '' ?>"
                data-assignedids="<?= esc_attr(json_encode($assignedIds)) ?>"
                data-taxonomy="<?= esc_attr($this->taxonomy->name) ?>">
            </select>
        </td>
        <?php
    }

    /**
     * @param array $assignedIds
     * @param string $name
     * @param string $type
     * @param int $siteId
     */
    private function renderInputs(array $assignedIds, string $name, string $type, int $siteId)
    {
        if (!in_array($type, ['radio', 'checkbox'], true)) {
            $type = $type === 'select' ? 'radio' : 'checkbox';
        }

        ?>
        <td colspan="2">
            <strong><?= esc_html($this->taxonomy->label) ?></strong>
            <ul>
                <?php
                wp_terms_checklist(
                    30,
                    [
                        'taxonomy' => $this->taxonomy->name,
                        'selected_cats' => $assignedIds,
                        'popular_cats' => false,
                        'checked_ontop' => false,
                        'walker' => new TaxonomyWalker($name, $type, $siteId),
                    ]
                );
                ?>
            </ul>
        </td>
        <?php
    }

    /**
     * @param int $termCount
     * @return string
     */
    private function inputType(int $termCount): string
    {
        static $singleTermTaxonomies;
        if (!is_array($singleTermTaxonomies)) {
            /**
             * Filter mutually exclusive taxonomies.
             *
             * @param string[] $taxonomies
             */
            $singleTermTaxonomies = (array)apply_filters(
                self::FILTER_SINGLE_TERM_TAXONOMIES,
                ['post_format']
            );
        }

        $threshold = (int)apply_filters(
            self::FILTER_TRANSLATION_UI_SELECT_THRESHOLD,
            1,
            $this->taxonomy->name,
            $termCount
        );

        $select = $termCount > $threshold;
        if (in_array($this->taxonomy->name, $singleTermTaxonomies, true)) {
            return $select ? 'select' : 'radio';
        }

        return $select ? 'select multiple' : 'checkbox';
    }
}
