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

use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel;

/**
 * WordPress "Language" site setting.
 */
class WordPressLanguageSiteSetting implements SiteSettingViewModel
{
    /**
     * @var string
     */
    private $id = 'locale';

    /**
     * @var string
     */
    private $wordPressLanguageSettingMarkup;

    /**
     * @param string $wordPressLanguageSettingMarkup
     */
    public function __construct(string $wordPressLanguageSettingMarkup)
    {
        $this->wordPressLanguageSettingMarkup = $wordPressLanguageSettingMarkup;
    }

    /**
     * @inheritdoc
     * phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
     */
    public function render(int $siteId)
    {
        echo $this->wordPressLanguageSettingMarkup;
        // phpcs:enabled
        echo wp_kses_post($this->description());
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return sprintf(
            '<label for="%2$s">%1$s</label>',
            esc_html__('WordPress Language', 'multilingualpress'),
            esc_attr($this->id)
        );
    }

    protected function description(): string
    {
        return sprintf(
            '<p class="description">%1$s</p>',
            esc_html__('This setting defines which language your site frontend should use', 'multilingualpress')
        );
    }
}
