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

use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel;
use Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicator;

/**
 * Site duplication "Plugins" setting.
 */
final class CopyUsersSetting implements SiteSettingViewModel
{
    /**
     * @var string
     */
    private $id = 'mlp-copy-users';

    /**
     * @inheritdoc
     */
    public function render(int $siteId)
    {
        ?>
        <label for="<?= esc_attr($this->id) ?>">
            <input
                type="checkbox"
                value="1"
                id="<?= esc_attr($this->id) ?>"
                name="<?= esc_attr(SiteDuplicator::NAME_COPY_USERS) ?>"
                disabled>
            <?php
            esc_html_e(
                'Copy the users from source site (the site selected in Based on site option)',
                'multilingualpress'
            )
            ?>
        </label>
        <?php
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return sprintf(
            '<label for="%2$s">%1$s</label>',
            esc_html__('Users', 'multilingualpress'),
            esc_attr($this->id)
        );
    }
}
