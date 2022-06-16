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

namespace Inpsyde\MultilingualPress\Core\Admin\Settings\Cache;

/**
 * Class CacheSettingsOptions
 * @package Inpsyde\MultilingualPress\Core\Admin
 */
class CacheSettingsOptionsView
{
    /**
     * @var CacheSettingsRepository
     */
    private $repository;

    /**
     * @var CacheSettingsOptions
     */
    private $cacheSettingsOptions;

    /**
     * CacheSettingsOptionsView constructor.
     * @param CacheSettingsRepository $repository
     * @param CacheSettingsOptions $cacheSettingsOptions
     */
    public function __construct(
        CacheSettingsRepository $repository,
        CacheSettingsOptions $cacheSettingsOptions
    ) {

        $this->repository = $repository;
        $this->cacheSettingsOptions = $cacheSettingsOptions;
    }

    /**
     * Render the Options Markup
     *
     * @return void
     */
    public function render()
    {
        $options = $this->repository->all();
        $optionsInfo = $this->cacheSettingsOptions->info();

        ?>
        <div class="mlp-internal-cache-settings">
            <?php
            foreach ($options as $groupName => $group) {
                $title = $optionsInfo[$groupName]['name'] ?: '';
                ?>
                <section class="mlp-internal-cache-settings-section">
                    <h4 class="mlp-internal-cache-settings-section__title">
                        <?= esc_html(sanitize_text_field($title)) ?>
                    </h4>
                    <table class="widefat mlp-settings-table mlp-settings-table--cache">
                        <tbody>
                        <?php $this->renderGroup($groupName, $group) ?>
                        </tbody>
                    </table>
                </section>
                <?php
            }
            ?>
        </div>
        <?php
    }

    /**
     * Render a Group of Options
     *
     * @param string $name
     * @param array $group
     */
    protected function renderGroup(string $name, array $group)
    {
        $optionsInfo = $this->cacheSettingsOptions->info();

        foreach ($group as $key => $value) {
            $idAttributeValue = CacheSettingsRepository::OPTION_NAME . "_{$key}";
            $nameAttributeValue = CacheSettingsRepository::OPTION_NAME . "[{$name}][{$key}]";
            $label = $optionsInfo[$name]['options'][$key]['label'] ?: '';
            $description = $optionsInfo[$name]['options'][$key]['description'] ?: '';
            ?>
            <tr>
                <th class="check-column" scope="row">
                    <input
                        type="checkbox"
                        id="<?= esc_attr($idAttributeValue) ?>"
                        name="<?= esc_attr($nameAttributeValue) ?>"
                        <?= checked(true, $value, false) ?>
                    />
                </th>
                <td>
                    <label for="<?= esc_attr($idAttributeValue) ?>">
                        <strong><?= esc_html($label) ?></strong>
                        <span><?= wp_kses($description, [], []) ?></span>
                    </label>
                </td>
            </tr>
            <?php
        }
    }
}
