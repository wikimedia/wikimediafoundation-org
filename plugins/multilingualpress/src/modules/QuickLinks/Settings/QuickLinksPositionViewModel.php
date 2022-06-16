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

use Inpsyde\MultilingualPress\Module\QuickLinks\Model\ViewModel;

/**
 * Class QuickLinksPositionViewModel
 * @package Inpsyde\MultilingualPress\Module\QuickLinks\Settings
 */
class QuickLinksPositionViewModel implements ViewModel
{
    const ID = 'position';

    /**
     * @var Repository
     */
    private $repository;

    /**
     * QuickLinksPositionViewModel constructor.
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function id(): string
    {
        return self::ID;
    }

    /**
     * @inheritDoc
     */
    public function title()
    {
        ?>
        <label for="position">
            <strong class="mlp-setting-name">
                <?= esc_html_x(
                    'QuickLinks Position',
                    'QuickLinks Module Settings',
                    'multilingualpress'
                ) ?>
            </strong>
        </label>
        <?php
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $positions = [
            'top-left' => _x(
                'Top Left',
                'QuickLinks Module Settings',
                'multilingualpress'
            ),
            'top-right' => _x(
                'Top Right',
                'QuickLinks Module Settings',
                'multilingualpress'
            ),
            'bottom-left' => _x(
                'Bottom Left',
                'QuickLinks Module Settings',
                'multilingualpress'
            ),
            'bottom-right' => _x(
                'Bottom Right',
                'QuickLinks Module Settings',
                'multilingualpress'
            ),
        ];

        $prefix = Repository::MODULE_SETTINGS;
        $quickLinksPositionSettingName = Repository::MODULE_SETTING_QUICKLINKS_POSITION;
        $position = $this->repository->position();

        foreach ($positions as $option => $label) : ?>
            <label for="<?= esc_attr("{$prefix}_{$quickLinksPositionSettingName}_{$option}") ?>"
                   class="quicklink-position-<?= sanitize_html_class($option) ?>"
            >
                <input type="radio"
                       id="<?= esc_attr("{$prefix}_{$quickLinksPositionSettingName}_{$option}") ?>"
                       name="<?= esc_attr("{$prefix}[{$quickLinksPositionSettingName}]") ?>"
                       value="<?= esc_attr($option) ?>"
                    <?= checked($option, $position, false) ?>
                />
                <?= esc_html($label) ?>
            </label>
            <?php
        endforeach;
    }
}
