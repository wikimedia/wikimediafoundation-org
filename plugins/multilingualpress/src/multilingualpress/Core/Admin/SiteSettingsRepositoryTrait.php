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

/**
 * Trait SiteSettingsRepositoryTrait
 */
trait SiteSettingsRepositoryTrait
{
    /**
     * Return an array where keys are site IDs and values are the setting value for the given key.
     *
     * @param string $settingKey
     * @return array
     */
    public function allSitesSetting(string $settingKey): array
    {
        $settings = $this->allSettings();
        $output = [];
        foreach ($settings as $siteId => $setting) {
            if (is_array($setting) && array_key_exists($settingKey, $setting)) {
                $output[$siteId] = $setting[$settingKey];
            }
        }

        return $output;
    }

    /**
     * Returns the complete settings data.
     *
     * @return array
     */
    public function allSettings(): array
    {
        return (array)get_network_option(0, SiteSettingsRepository::OPTION, []);
    }

    /**
     * Sets the given settings data.
     *
     * @param array $settings
     * @return bool
     */
    public function updateSettings(array $settings): bool
    {
        return (bool)update_network_option(
            0,
            SiteSettingsRepository::OPTION,
            $settings
        );
    }

    /**
     * Updates the given setting for the site with the given ID, or the current site.
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $siteId
     * @return bool
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    private function updateSetting(
        string $key,
        $value,
        int $siteId = null
    ): bool {

        $siteId = $siteId ?: get_current_blog_id();
        $settings = $this->allSettings();
        if (!isset($settings[$siteId]) || !is_array($settings[$siteId])) {
            $settings[$siteId] = [];
        }

        $settings[$siteId][$key] = $value;

        return $this->updateSettings($settings);
    }
}
