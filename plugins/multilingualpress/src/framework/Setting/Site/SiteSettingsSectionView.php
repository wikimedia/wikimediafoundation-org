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

namespace Inpsyde\MultilingualPress\Framework\Setting\Site;

/**
 * Site setting view implementation for a whole settings section.
 */
final class SiteSettingsSectionView implements SiteSettingView
{
    const ACTION_AFTER = 'multilingualpress.after_site_settings';

    const ACTION_BEFORE = 'multilingualpress.before_site_settings';

    /**
     * @var SiteSettingsSectionViewModel
     */
    private $model;

    /**
     * @param SiteSettingsSectionViewModel $model
     */
    public function __construct(SiteSettingsSectionViewModel $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritdoc
     */
    public function render(int $siteId = 0): bool
    {
        echo wp_kses_post($this->model->title());
        $modelId = $this->model->id();
        ?>
        <table class="form-table section-<?= sanitize_html_class($modelId) ?>">
            <?php

            /**
             * Fires right before the settings are rendered.
             *
             * @param int $siteId
             * @param string $modelId
             */
            do_action(static::ACTION_BEFORE, $siteId, $modelId);

            /**
             * Fires right before the settings are rendered.
             *
             * @param int $siteId
             */
            do_action(static::ACTION_BEFORE . "_{$modelId}", $siteId);

            $this->model->renderView($siteId);

            /**
             * Fires right after the settings have been rendered.
             *
             * @param int $siteId
             * @param string $modelId
             */
            do_action(static::ACTION_AFTER, $siteId, $modelId);

            /**
             * Fires right after the settings have been rendered.
             *
             * @param int $siteId
             */
            do_action(static::ACTION_AFTER . "_{$modelId}", $siteId);
            ?>
        </table>
        <?php
        return true;
    }
}
