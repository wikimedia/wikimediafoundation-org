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
 * Class CopyAttachmentsSetting
 * @package Inpsyde\MultilingualPress\SiteDuplication
 */
final class CopyAttachmentsSetting implements SiteSettingViewModel
{
    /**
     * @inheritDoc
     */
    public function render(int $siteId)
    {
        ?>
        <label for="<?= esc_attr(SiteDuplicator::NAME_COPY_ATTACHMENTS) ?>">
            <input
                type="checkbox"
                value="1"
                id="<?= esc_attr(SiteDuplicator::NAME_COPY_ATTACHMENTS) ?>"
                name="<?= esc_attr(SiteDuplicator::NAME_COPY_ATTACHMENTS) ?>"
                disabled>
            <?php
            esc_html_e(
                'Copy the attachments to the new site.',
                'multilingualpress'
            );
            ?>
        </label>
        <p class="description">
            <?=
            wp_kses(
                _x(
                    'If you turn it off you will need to copy the directories manually.<br/>Turning it off is sometimes useful if you have problems with copying the attachments automatically due to hosting restrictions.',
                    'Site Duplication Setting',
                    'multilingualpress'
                ),
                ['br' => true]
            );
            ?>
        </p>
        <?php
    }

    /**
     * @inheritDoc
     */
    public function title(): string
    {
        return sprintf(
            '<label for="%2$s">%1$s</label>',
            esc_html__('Copy Attachments', 'multilingualpress'),
            esc_attr(SiteDuplicator::NAME_COPY_ATTACHMENTS)
        );
    }
}
