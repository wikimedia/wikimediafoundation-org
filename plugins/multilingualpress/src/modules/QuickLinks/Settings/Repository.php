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

namespace Inpsyde\MultilingualPress\Module\QuickLinks\Settings;

/**
 * Class Repository
 * @package Inpsyde\MultilingualPress\Module\QuickLinks
 */
class Repository
{
    const MODULE_SETTINGS = 'multilingualpress_module_quicklinks_settings';
    const MODULE_SETTING_QUICKLINKS_POSITION = 'position';
    const ALLOWED_QUICKLINKS_POSITIONS = [
        'top-left',
        'top-right',
        'bottom-left',
        'bottom-right',
    ];

    /**
     * Retrieve the position for Quick Links
     *
     * @return string
     */
    public function position(): string
    {
        $options = $this->moduleSettings();

        return $options[self::MODULE_SETTING_QUICKLINKS_POSITION] ?? 'bottom-left';
    }

    /**
     * Update Position Setting
     *
     * @param string $position
     */
    public function updatePosition(string $position)
    {
        if (!\in_array($position, self::ALLOWED_QUICKLINKS_POSITIONS, true)) {
            return;
        }

        $options = $this->moduleSettings();

        $options[self::MODULE_SETTING_QUICKLINKS_POSITION] = $position;

        $this->updateModuleSettings($options);
    }

    /**
     * Retrieve the Module Settings
     *
     * @return array
     */
    protected function moduleSettings(): array
    {
        return (array)get_network_option(null, self::MODULE_SETTINGS, []);
    }

    /**
     * Update the Given Module Settings
     *
     * @param array $options
     * @return void
     */
    protected function updateModuleSettings(array $options)
    {
        update_network_option(null, self::MODULE_SETTINGS, $options);
    }
}
