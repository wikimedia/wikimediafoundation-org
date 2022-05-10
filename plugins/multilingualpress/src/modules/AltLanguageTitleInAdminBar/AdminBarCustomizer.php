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

namespace Inpsyde\MultilingualPress\Module\AltLanguageTitleInAdminBar;

/**
 * Replaces the site names in the admin bar with the respective alternative language titles.
 */
class AdminBarCustomizer
{
    /**
     * @var SettingsRepository
     */
    private $settingsRepository;

    /**
     * @param SettingsRepository $siteSettingsRepository
     */
    public function __construct(SettingsRepository $siteSettingsRepository)
    {
        $this->settingsRepository = $siteSettingsRepository;
    }

    /**
     * Replaces the current site's name with the site's alternative language title, if not empty.
     *
     * @param \WP_Admin_Bar $adminBar
     * @return \WP_Admin_Bar
     */
    public function replaceSiteName(\WP_Admin_Bar $adminBar): \WP_Admin_Bar
    {
        $siteId = get_current_blog_id();
        $title = $this->settingsRepository->alternativeLanguageTitle($siteId);

        if (!$title) {
            return $adminBar;
        }

        $adminBar->add_node(['id' => 'site-name', 'title' => $title]);

        return $adminBar;
    }

    /**
     * Replaces all site names with the individual site's alternative language title, if not empty.
     *
     * @param \WP_Admin_Bar $adminBar
     * @return \WP_Admin_Bar
     */
    public function replaceSiteNodes(\WP_Admin_Bar $adminBar): \WP_Admin_Bar
    {
        if (empty($adminBar->user->blogs)) {
            return $adminBar;
        }

        foreach ((array)$adminBar->user->blogs as $site) {
            if (empty($site->userblog_id)) {
                continue;
            }

            $siteId = (int)$site->userblog_id;
            $title = $this->settingsRepository->alternativeLanguageTitle($siteId);
            if (!$title) {
                continue;
            }

            $adminBar->user->blogs[$site->userblog_id]->blogname = $title;
        }

        return $adminBar;
    }
}
