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
use Inpsyde\MultilingualPress\TranslationUi\Term\Ajax\Search;
use Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext;
use Inpsyde\MultilingualPress\TranslationUi\Term\MetaboxFields;

use function Inpsyde\MultilingualPress\siteLanguageName;

class Relation
{
    const VALUES = [
        MetaboxFields::FIELD_RELATION_NEW,
        MetaboxFields::FIELD_RELATION_EXISTING,
        MetaboxFields::FIELD_RELATION_REMOVE,
        MetaboxFields::FIELD_RELATION_LEAVE,
    ];

    /**
     * @param $value
     * @return string
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public static function sanitize($value): string
    {
        // phpcs:enable

        if (!is_scalar($value) || in_array((string)$value, self::VALUES, true)) {
            return MetaboxFields::FIELD_RELATION_LEAVE;
        }

        return (string)$value;
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $context
     */
    public function __invoke(MetaboxFieldsHelper $helper, RelationshipContext $context)
    {
        $language = siteLanguageName($context->remoteSiteId());

        $hasRemoteTerm = $context->hasRemoteTerm();
        $currently = __('Currently not connected.', 'multilingualpress');
        $currentlyMarkupFormat = '<strong>%s</strong>';
        if ($hasRemoteTerm) {
            // translators: %s is the term name
            $format = __('Currently connected with "%s"', 'multilingualpress');

            $editTermTitle = $context->remoteTerm()->name;
            $editTermLinkMarkup = '';
            $editTermLink = get_edit_term_link(
                $context->remoteTermId(),
                $context->remoteTerm()->taxonomy
            );

            if (wp_http_validate_url($editTermLink)) {
                $editTermLinkMarkupFormat = '<a class="mlp-entity-edit-link" href="%1$s">%2$s</a>';
                $editTermLinkMarkup = sprintf(
                    $editTermLinkMarkupFormat,
                    esc_url($editTermLink),
                    $editTermTitle
                );
            }

            $currently = sprintf(
                $format,
                $editTermLinkMarkup ?: $editTermTitle
            );

            $currentlyMarkupFormat = '<div class="currently-connected">%s</div>';
        }

        ?>
        <tr class="main-row">
            <td>
                <?= sprintf($currentlyMarkupFormat, wp_kses_post($currently)) ?>
                <?php
                $this->leaveConnectionField($helper, $hasRemoteTerm);
                if ($hasRemoteTerm) {
                    $this->removeConnectionField($helper, $language);
                }
                if (!$hasRemoteTerm) {
                    $this->newTermField($helper, $language);
                }
                $this->existingTermField($helper, $language);
                ?>
            </td>
        </tr>
        <?php
        $this->searchRow($helper);
        $this->buttonRow();
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param string $key
     * @return string[]
     */
    private function idAndName(MetaboxFieldsHelper $helper, string $key): array
    {
        $base = MetaboxFields::FIELD_RELATION;
        $id = $helper->fieldId("{$base}-{$key}");
        $name = $helper->fieldName($base);

        return [$id, $name];
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param string $language
     */
    protected function newTermField(MetaboxFieldsHelper $helper, string $language)
    {
        $key = MetaboxFields::FIELD_RELATION_NEW;
        list($id, $name) = $this->idAndName($helper, $key);

        ?>
        <p>
            <label for="<?= esc_attr($id) ?>">
                <input
                    type="radio"
                    id="<?= esc_attr($id) ?>"
                    value="<?= esc_attr($key) ?>"
                    name="<?= esc_attr($name) ?>">
                <?php
                // translators: %s is the language name
                $format = __(
                    'Create a new term, and use it as translation in %s.',
                    'multilingualpress'
                );
                print esc_html(sprintf($format, $language))
                ?>
            </label>
        </p>
        <?php
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param string $language
     */
    protected function existingTermField(MetaboxFieldsHelper $helper, string $language)
    {
        $key = MetaboxFields::FIELD_RELATION_EXISTING;
        list($id, $name) = $this->idAndName($helper, $key);

        ?>
        <p>
            <label for="<?= esc_attr($id) ?>">
                <input
                    type="radio"
                    id="<?= esc_attr($id) ?>"
                    value="<?= esc_attr($key) ?>"
                    name="<?= esc_attr($name) ?>">
                <?php
                // translators: %s is the language name
                $format = __(
                    'Select an existing term to be used as translation in %s.',
                    'multilingualpress'
                );
                print esc_html(sprintf($format, $language))
                ?>
            </label>
        </p>
        <?php
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param string $language
     */
    protected function removeConnectionField(MetaboxFieldsHelper $helper, string $language)
    {
        $key = MetaboxFields::FIELD_RELATION_REMOVE;
        list($id, $name) = $this->idAndName($helper, $key);

        ?>
        <p>
            <label for="<?= esc_attr($id) ?>">
                <input
                    type="radio"
                    id="<?= esc_attr($id) ?>"
                    value="<?= esc_attr($key) ?>"
                    name="<?= esc_attr($name) ?>">
                <?php
                // translators: %s is the language name
                $format = __(
                    'Remove connection (don\'t translate in %s).',
                    'multilingualpress'
                );
                print esc_html(sprintf($format, $language))
                ?>
            </label>
        </p>
        <?php
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @param bool $hasRemoteTerm
     */
    protected function leaveConnectionField(MetaboxFieldsHelper $helper, bool $hasRemoteTerm)
    {
        $value = $hasRemoteTerm
            ? MetaboxFields::FIELD_RELATION_LEAVE
            : MetaboxFields::FIELD_RELATION_NOTHING;

        list($id, $name) = $this->idAndName($helper, $value);

        $label = $hasRemoteTerm
            ? __('Do not change connected term.', 'multilingualpress')
            : __('Keep not connected.', 'multilingualpress');

        ?>
        <p>
            <label for="<?= esc_attr($id) ?>">
                <input
                    type="radio"
                    id="<?= esc_attr($id) ?>"
                    value="<?= esc_attr($value) ?>"
                    name="<?= esc_attr($name) ?>"
                    checked>
                <?= esc_html($label) ?>
            </label>
        </p>
        <?php
    }

    /**
     * @param MetaboxFieldsHelper $helper
     * @return void
     */
    protected function searchRow(MetaboxFieldsHelper $helper)
    {
        $name = $helper->fieldName(MetaboxFields::FIELD_RELATION_SEARCH);
        $inputId = $helper->fieldId(MetaboxFields::FIELD_RELATION_SEARCH);
        $resultsId = $helper->fieldId('search-results');
        $placeholder = __('Start typing to search...', 'multilingualpress');
        ?>
        <tr class="search-input-row" style="display: none">
            <td>
                <input
                    id="<?= esc_attr($inputId) ?>"
                    type="text"
                    class="regular-text"
                    data-results="#<?= esc_attr($resultsId) ?>"
                    data-action="<?= esc_attr(Search::ACTION) ?>"
                    placeholder="<?= esc_attr($placeholder) ?>"
                    aria-label="<?= esc_attr__('Search', 'multilingualpress') ?>">
            </td>
        </tr>
        <tr>
            <td id="<?= esc_attr($resultsId) ?>" class="search-results" style="display: none">
                <table class="widefat striped">
                    <tbody>
                    <tr class="search-results-row" style="display: none">
                        <td>
                            <label>
                                <input
                                    type="radio"
                                    name="<?= esc_attr($name) ?>"
                                    value="0"
                                    aria-label="">
                                <span></span>
                            </label>
                        </td>
                    </tr>
                    <tr class="search-results-none" style="display: none">
                        <td colspan="2">
                            <?php
                            esc_html_e(
                                'No terms found matching search.',
                                'multilingualpress'
                            );
                            ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <?php
    }

    /**
     * @return void
     */
    protected function buttonRow()
    {
        ?>
        <tr>
            <td>
                <button
                    style="display:none;"
                    class="button-primary update-relationship">
                    <?php esc_html_e('Update now', 'multilingualpress') ?>
                </button>
            </td>
        </tr>
        <?php
    }
}
