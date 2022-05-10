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

use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel;

/**
 * hreflang: X-default site setting.
 */
final class XDefaultSiteSetting implements SiteSettingViewModel
{
    /**
     * @var string
     */
    private $id = 'mlp-xdefault';

    /**
     * @var SiteSettingsRepository
     */
    private $siteSettingsRepository;

    /**
     * @var SiteRelations
     */
    private $siteRelations;

    /**
     * XDefaultSiteSetting constructor.
     * @param SiteRelations $siteRelations
     * @param SiteSettingsRepository $siteSettingsRepository
     */
    public function __construct(
        SiteRelations $siteRelations,
        SiteSettingsRepository $siteSettingsRepository
    ) {

        $this->siteRelations = $siteRelations;
        $this->siteSettingsRepository = $siteSettingsRepository;
    }

    /**
     * @inheritdoc
     */
    public function render(int $siteId)
    {
        $name = SiteSettingsRepository::NAME_XDEFAULT;
        ?>
        <select id="<?= esc_attr($this->id) ?>" name="<?= esc_attr($name) ?>">
            <option value="0"><?= esc_html__('None', 'multilingualpress'); ?></option>
            <?php $this->renderOptions($siteId); ?>
        </select>
        <p class="description">
            <?=
            esc_html__(
                'Select a default site as fallback when no page translation is found.',
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
            esc_html__('hreflang: X-default', 'multilingualpress'),
            esc_attr($this->id)
        );
    }

    /**
     * Render Options List
     *
     * @return void
     */
    private function renderOptions(int $siteId)
    {
        $relatedSites = $this->relatedSites($siteId);
        $xDefault = $this->siteSettingsRepository
            ->allSitesSetting(SiteSettingsRepository::NAME_XDEFAULT);

        foreach ($relatedSites as $site) {
            $currentSiteId = (int)$site->blog_id;
            $url = untrailingslashit($site->domain) . trailingslashit($site->path);

            printf(
                '<option value="%1$s" %2$s>%3$s</option>',
                esc_attr($currentSiteId),
                selected($currentSiteId, $xDefault[$siteId] ?? '', false),
                esc_url($url)
            );
        }
    }

    /**
     * Retrieve all the related sites according to the given parameter
     *
     * @param int $siteId
     * @return array
     */
    private function relatedSites(int $siteId): array
    {
        $sites = $this->siteRelations->relatedSiteIds($siteId, true);

        foreach ($sites as &$site) {
            $site = get_site($site);
        }

        return $sites;
    }
}
