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

namespace Inpsyde\MultilingualPress\Core\Admin;

use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use Inpsyde\MultilingualPress\Core\TaxonomyRepository;

use function Inpsyde\MultilingualPress\printNonceField;

/**
 * Taxonomy settings tab view.
 */
final class TaxonomySettingsTabView implements SettingsPageView
{

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var TaxonomyRepository
     */
    private $repository;

    /**
     * @param TaxonomyRepository $repository
     * @param Nonce $nonce
     */
    public function __construct(TaxonomyRepository $repository, Nonce $nonce)
    {
        $this->repository = $repository;
        $this->nonce = $nonce;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $taxonomies = $this->repository->allAvailableTaxonomies();
        if (!$taxonomies) {
            return;
        }

        printNonceField($this->nonce);
        ?>
        <table class="widefat mlp-settings-table mlp-taxonomy-settings">
            <tbody>
            <?php array_walk($taxonomies, [$this, 'renderTableRow']) ?>
            </tbody>
            <thead>
            <?php $this->renderTableHeadings() ?>
            </thead>
            <tfoot>
            <?php $this->renderTableHeadings() ?>
            </tfoot>
        </table>
        <?php
    }

    /**
     * Returns the input ID for the given taxonomy slug and settings field name.
     *
     * @param string $slug
     * @param string $field
     * @return string
     */
    private function fieldId(string $slug, string $field = ''): string
    {
        return "mlp-taxonomy-{$slug}" . ($field ? "-{$field}" : '');
    }

    /**
     * Returns the input name for the given taxonomy slug and settings field name.
     *
     * @param string $slug
     * @param string $field
     * @return string
     */
    private function fieldName(string $slug, string $field): string
    {
        return TaxonomySettingsUpdater::SETTINGS_NAME . "[{$slug}][{$field}]";
    }

    /**
     * Renders the table headings.
     */
    private function renderTableHeadings()
    {
        ?>
        <tr>
            <th scope="col"></th>
            <th scope="col"><?php esc_html_e('Taxonomy', 'multilingualpress') ?></th>
        </tr>
        <?php
    }

    /**
     * Renders a table row element according to the given data.
     *
     * @param \WP_Taxonomy $taxonomy
     * @param string $slug
     */
    private function renderTableRow(\WP_Taxonomy $taxonomy, string $slug)
    {
        $isActive = $this->repository->isTaxonomyActive($slug);
        ?>
        <tr class="<?= esc_attr($isActive ? 'active' : 'inactive') ?>">
            <?php
            $field = TaxonomySettingsUpdater::SETTINGS_FIELD_ACTIVE;
            $id = $this->fieldId($slug);
            ?>
            <th class="check-column" scope="row">
                <input
                    type="checkbox"
                    name="<?= esc_attr($this->fieldName($slug, $field)) ?>"
                    value="1"
                    id="<?= esc_attr($id) ?>"
                    title="<?= esc_attr($slug) ?>"
                    <?php checked($isActive) ?>>
            </th>
            <td>
                <label for="<?= esc_attr($id) ?>" class="mlp-block-label">
                    <strong class="mlp-setting-name" title="<?= esc_attr($slug) ?>">
                        <?= esc_html($taxonomy->labels->name) ?>
                    </strong>
                </label>
            </td>
        </tr>
        <?php
    }
}
