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

namespace Inpsyde\MultilingualPress\Core\Admin\Settings;

use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel;

/**
 * Add MultilingualPress Settings into WordPress site settings screen
 */
class WordPressSettingsScreen
{

    /**
     * @var array
     */
    private $settings;

    /**
     * @var SiteSettingsRepository
     */
    private $repository;

    /**
     * @param array<SiteSettingViewModel> $settings The list of settings
     * @param SiteSettingsRepository $repository
     */
    public function __construct(array $settings, SiteSettingsRepository $repository)
    {
        $this->settings = $settings;
        $this->repository = $repository;
    }

    /**
     * Create the MultilingualPress settings in WordPress site settings screen
     *
     * @see https://developer.wordpress.org/reference/functions/add_settings_field/ add_settings_field
     */
    public function addSettings()
    {
        foreach ($this->settings as $setting) {
            add_settings_field(
                SiteSettingsRepository::NAME_LANGUAGE,
                $setting->title(),
                static function () use ($setting) {
                    $setting->render(get_current_blog_id());
                },
                'general',
                'default',
                ['class' => SiteSettingsRepository::NAME_LANGUAGE]
            );
            register_setting('general', SiteSettingsRepository::NAME_LANGUAGE, 'esc_attr');
        }
    }

    /**
     * Update the MultilingualPress language
     *
     * When the WordPress settings are updated, we also need to update MultilingualPress language.
     * The MultilingualPress Language setting is added in WordPress General Settings screen.
     *
     * @see https://developer.wordpress.org/reference/hooks/update_option/ update_option
     *
     * @param string $option The option name
     * @param $oldValue (mixed) The old option value.
     * @param $value (mixed) The new option value.
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function saveSettings(string $option, $oldValue, $value)
    {
        // phpcs:enable

        if ($option !== SiteSettingsRepository::NAME_LANGUAGE) {
            return;
        }

        $this->repository->updateLanguage((string)$value, get_current_blog_id());
    }
}
