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

use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView;

/**
 * Class PostTypeSlugsSettingsSectionView
 */
final class PostTypeSlugsSettingsSectionView implements SiteSettingView
{
    const ACTION_AFTER = 'multilingualpress.after_permalink_site_settings';
    const ACTION_BEFORE = 'multilingualpress.before_permalink_site_settings';

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
    public function render(int $siteId): bool
    {
        echo wp_kses_post($this->model->title());
        ?>
        <table class="form-table section-permalink-site-settings">
            <?php

            /**
             * Fires right before the settings are rendered.
             *
             * @param int $siteId
             */
            do_action(self::ACTION_AFTER, $siteId);

            $this->model->renderView($siteId);

            /**
             * Fires right after the settings have been rendered.
             *
             * @param int $siteId
             */
            do_action(self::ACTION_BEFORE, $siteId);
            ?>
        </table>
        <?php
        return true;
    }
}
