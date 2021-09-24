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

namespace Inpsyde\MultilingualPress\Module\Redirect\Settings;

use WP_Site;

/**
 * Class RedirectFallbackViewModel
 * @package Inpsyde\MultilingualPress\Module\Redirect\Settings
 */
class RedirectFallbackViewRenderer implements ViewRenderer
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * RedirectFallbackViewModel constructor.
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function title()
    {
        ?>
        <label for="redirect_fallback">
            <strong class="mlp-setting-name">
                <?= esc_html_x(
                    'Redirect Fallback',
                    'Redirect Module Settings',
                    'multilingualpress'
                ) ?>
            </strong>
        </label>
        <?php
    }

    /**
     * @inheritDoc
     */
    public function content()
    {
        $sites = $this->sites();
        if (count($sites) <= 1) {
            return;
        }

        $prefix = Repository::MODULE_SETTINGS;
        $redirectFallbackIdSettingName = Repository::MODULE_SETTING_FALLBACK_REDIRECT_SITE_ID;

        $selectedSiteId = $this->repository->redirectFallbackSiteId();

        ?>
        <select
            id="<?= esc_attr("{$prefix}_${redirectFallbackIdSettingName}") ?>"
            name="<?= esc_attr("{$prefix}[${redirectFallbackIdSettingName}]") ?>">
            <?php $this->renderOptionsForSites($sites, $selectedSiteId) ?>
        </select>
        <p class="mlp-settings-table__option-description">
            <?= esc_html_x(
                'Choose where to redirect the user when the browser language does not correspond to any available language sites in the network.',
                'Redirect Module Settings',
                'multilingualpress'
            ) ?>
        </p>
        <?php
    }

    /**
     * Render the Options List of Sites that can be selected
     *
     * @param WP_Site[] $sites
     * @param int $selected
     */
    protected function renderOptionsForSites(array $sites, int $selected)
    {
        if (count($sites) <= 1) {
            return;
        }

        printf(
            '<option value="0">%s</option>',
            esc_html_x('None', 'Redirect Module Settings', 'multilingualpress')
        );

        /** @var WP_Site $site */
        foreach ($sites as $site) {
            $this->renderOption($site, $selected);
        }
    }

    /**
     * Render a Single Option
     *
     * @param WP_Site $site
     * @param int $selected
     */
    protected function renderOption(WP_Site $site, int $selected)
    {
        $siteId = $site->id;
        $siteUrl = $site->siteurl;

        $siteId and $siteUrl and printf(
            '<option value="%1$d"%2$s>%3$s</option>',
            esc_attr($siteId),
            selected($selected, $siteId, false),
            esc_url($siteUrl)
        );
    }

    /**
     * Retrieve the Existing Sites
     *
     * @return array
     */
    protected function sites(): array
    {
        return get_sites(['orderby' => 'id']);
    }
}
