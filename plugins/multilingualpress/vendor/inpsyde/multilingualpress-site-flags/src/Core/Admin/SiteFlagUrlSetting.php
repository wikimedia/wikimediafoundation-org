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

namespace Inpsyde\MultilingualPress\SiteFlags\Core\Admin;

use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel;

/**
 * MultilingualPress "Site Custom Flag Url" site setting
 */
final class SiteFlagUrlSetting implements SiteSettingViewModel
{
    /**
     * @var string
     */
    private $id = 'mlp-site-flag-url';

    /**
     * @var SiteSettingsRepository
     */
    private $repository;

    /**
     * @param SiteSettingsRepository $repository
     */
    public function __construct(SiteSettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function render(int $siteId)
    {
        ?>
        <input
            type="text"
            name="<?= esc_attr(SiteSettingsRepository::KEY_SITE_FLAG_URL) ?>"
            value="<?= esc_attr($this->repository->siteFlagUrl($siteId)) ?>"
            class="regular-text"
            id="<?= esc_attr($this->id) ?>">
        <?php
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return sprintf(
            '<label for="%2$s">%1$s</label>',
            esc_html__('Custom Site Flag', 'multilingualpress'),
            esc_attr($this->id)
        );
    }
}
