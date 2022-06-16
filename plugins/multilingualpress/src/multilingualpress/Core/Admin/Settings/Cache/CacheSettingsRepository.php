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

namespace Inpsyde\MultilingualPress\Core\Admin\Settings\Cache;

use DomainException;

/**
 * Class CacheSettingsRepository
 * @package Inpsyde\MultilingualPress\Core\Admin
 */
class CacheSettingsRepository
{
    const OPTION_NAME = 'multilingualpress_internal_cache_setting';

    /**
     * @var CacheSettingsOptions
     */
    private $settingsOptions;

    /**
     * @var CacheSettingNamesValidator
     */
    private $cacheSettingNamesValidator;

    /**
     * CacheSettingsRepository constructor.
     * @param CacheSettingsOptions $cacheSettingsOptions
     * @param CacheSettingNamesValidator $cacheSettingNamesValidator
     */
    public function __construct(
        CacheSettingsOptions $cacheSettingsOptions,
        CacheSettingNamesValidator $cacheSettingNamesValidator
    ) {

        $this->settingsOptions = $cacheSettingsOptions;
        $this->cacheSettingNamesValidator = $cacheSettingNamesValidator;
    }

    /**
     * Retrieve All Options
     *
     * @return array
     */
    public function all(): array
    {
        return $this->optionsFromDatabase();
    }

    /**
     * Get Single Cache Setting
     *
     * @param string $group
     * @param string $key
     * @return bool
     */
    public function get(string $group, string $key): bool
    {
        $settings = $this->all();

        return isset($settings[$group][$key]) and $settings[$group][$key];
    }

    /**
     * Update Settings
     *
     * @param array $settings
     * @return bool
     * @throws DomainException
     */
    public function update(array $settings): bool
    {
        if (!$this->cacheSettingNamesValidator->allowed($settings)) {
            throw new DomainException(
                'Cache settings cannot be stored because the collection does not reflect the allowed settings'
            );
        }

        return update_network_option(0, self::OPTION_NAME, $settings);
    }

    /**
     * Fill Options With Values From Database
     *
     * @return array
     */
    protected function optionsFromDatabase(): array
    {
        $defaults = $this->settingsOptions->defaults();
        $options = get_network_option(0, self::OPTION_NAME, []) ?: [];
        $filledOptions = [];

        if (!$options) {
            return $defaults;
        }

        foreach ($defaults as $groupName => $groupOptions) {
            foreach ($groupOptions as $key => $value) {
                /*
                 * Keep the original default value or set the one from the database.
                 *
                 * In case of new options not already in db we'll get the default value
                 * instead of false.
                 */
                $value = isset($options[$groupName][$key]) ? $options[$groupName][$key] : $value;

                $filledOptions[$groupName][$key] = $value;
            }
        }

        return $filledOptions;
    }
}
