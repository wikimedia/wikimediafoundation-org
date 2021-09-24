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
use Inpsyde\MultilingualPress\Core\PostTypeRepository;

use function Inpsyde\MultilingualPress\printNonceField;

/**
 * Post type settings tab view.
 */
final class PostTypeSettingsTabView implements SettingsPageView
{

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var PostTypeRepository
     */
    private $repository;

    /**
     * @param PostTypeRepository $repository
     * @param Nonce $nonce
     */
    public function __construct(PostTypeRepository $repository, Nonce $nonce)
    {
        $this->repository = $repository;
        $this->nonce = $nonce;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $postTypes = $this->repository->allAvailablePostTypes();
        if (!$postTypes) {
            return;
        }

        printNonceField($this->nonce);
        ?>
        <table class="widefat mlp-settings-table mlp-post-type-settings">
            <tbody>
            <?php array_walk($postTypes, [$this, 'renderTableRow']) ?>
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
     * Returns the input ID for the given post type slug and settings field name.
     *
     * @param string $postTypeSlug
     * @param string $filedName
     * @return string
     */
    private function fieldId(string $postTypeSlug, string $filedName = ''): string
    {
        return "mlp-post-type-{$postTypeSlug}" . ($filedName ? "-{$filedName}" : '');
    }

    /**
     * Returns the input name for the given post type slug and settings field name.
     *
     * @param string $slug
     * @param string $field
     * @return string
     */
    private function fieldName(string $slug, string $field): string
    {
        return PostTypeSettingsUpdater::SETTINGS_NAME . "[{$slug}][{$field}]";
    }

    /**
     * Renders the table headings.
     */
    private function renderTableHeadings()
    {
        ?>
        <tr>
            <th scope="col"></th>
            <th scope="col"><?php esc_html_e('Post Type', 'multilingualpress') ?></th>
            <th scope="col"><?php esc_html_e('Permalinks', 'multilingualpress') ?></th>
        </tr>
        <?php
    }

    /**
     * Renders a table row element according to the given data.
     *
     * @param \WP_Post_Type $postType
     * @param string $slug
     */
    private function renderTableRow(\WP_Post_Type $postType, string $slug)
    {
        $isActive = $this->repository->isPostTypeActive($slug);
        ?>
    <tr class="<?= esc_attr($isActive ? 'active' : 'inactive') ?>">
        <?php
        $field = PostTypeSettingsUpdater::SETTINGS_FIELD_ACTIVE;
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
                    <?= esc_html($postType->labels->name) ?>
                </strong>
            </label>
        </td>
        <?php
        $field = PostTypeSettingsUpdater::SETTINGS_FIELD_PERMALINKS;
        $id = $this->fieldId($slug, $field);

        if (!$postType->_builtin) {
            ?>
            <td>
                <label for="<?= esc_attr($id) ?>" class="mlp-block-label">
                    <input
                        type="checkbox"
                        name="<?= esc_attr($this->fieldName($slug, $field)) ?>"
                        value="1"
                        id="<?= esc_attr($id) ?>"
                        <?php checked($this->repository->isPostTypeQueryBased($slug)) ?>>
                    <?php esc_html_e('Use dynamic permalinks', 'multilingualpress') ?>
                </label>
            </td>
            </tr>
            <?php
        }
    }
}
