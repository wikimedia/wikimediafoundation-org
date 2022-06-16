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
 * Class SiteMenuLanguageStyleSetting
 */
final class SiteMenuLanguageStyleSetting implements SiteSettingViewModel
{
    const FLAG_AND_LANGUAGES = 'flag_and_text';
    const ONLY_FLAGS = 'only_flag';
    const ONLY_LANGUAGES = 'only_language';

    /**
     * @var string
     */
    private $id = 'mlp-site-menu-language-style';

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
        $selected = $this->repository->siteMenuLanguageStyle($siteId);
        ?>
        <select
            name="<?= esc_attr(SiteSettingsRepository::KEY_SITE_MENU_LANGUAGE_STYLE) ?>"
            id="<?= esc_attr($this->id) ?>"
        >
            <?php foreach (self::options() as $value => $label) : ?>
                <option value="<?= esc_attr($value) ?>" <?php selected($selected, $value) ?>>
                    <?= esc_html($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return sprintf(
            '<label for="%2$s">%1$s</label>',
            esc_html__('Menu Language Style', 'multilingualpress'),
            esc_attr($this->id)
        );
    }

    /**
     * @return array
     */
    private static function options(): array
    {
        return [
            self::FLAG_AND_LANGUAGES => __('Flags and Languages', 'multilingualpress'),
            self::ONLY_FLAGS => __('Only Flags', 'multilingualpress'),
            self::ONLY_LANGUAGES => __('Only Languages', 'multilingualpress'),
        ];
    }
}
