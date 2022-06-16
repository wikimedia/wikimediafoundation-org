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

namespace Inpsyde\MultilingualPress\SiteDuplication\Settings;

use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsUpdater;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel;

/**
 * Site duplication "Search Engine Visibility" setting.
 */
final class SearchEngineVisibilitySetting implements SiteSettingViewModel
{
    const FILTER_SEARCH_ENGINE_VISIBILITY = 'multilingualpress.search_engine_visibility';

    /**
     * @var string
     */
    private $fieldId = 'mlp-search-engine-visibility';

    /**
     * @inheritdoc
     */
    public function render(int $siteId)
    {
        /**
         * Filters the default search engine visibility value when adding a new site.
         *
         * @param bool $visible
         */
        $visible = apply_filters(
            self::FILTER_SEARCH_ENGINE_VISIBILITY,
            true
        );
        ?>
        <label for="<?= esc_attr($this->fieldId) ?>">
            <input
                type="checkbox"
                value="0"
                id="<?= esc_attr($this->fieldId) ?>"
                name="<?= esc_attr(SiteSettingsUpdater::NAME_SEARCH_ENGINE_VISIBILITY) ?>"
                <?php checked(!$visible) ?>>
            <?php
            esc_html_e(
                'Discourage search engines from indexing this site',
                'multilingualpress'
            );
            ?>
        </label>
        <p class="description">
            <?php
            esc_html_e(
                'It is up to search engines to honor this request.',
                'multilingualpress'
            );
            ?>
        </p>
        <?php
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return sprintf(
            '<label for="%2$s">%1$s</label>',
            esc_html__('Search Engine Visibility', 'multilingualpress'),
            esc_attr($this->fieldId)
        );
    }
}
