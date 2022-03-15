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

namespace Inpsyde\MultilingualPress\Module\LanguageSwitcher;

use function Inpsyde\MultilingualPress\sanitizeHtmlClass;

class View
{
    const FILTER_ITEM_LANGUAGE_NAME = 'multilingualpress.language_switcher_item_language_name';
    const FILTER_ITEM_FLAG_URL = 'multilingualpress.language_switcher_item_flag_url';

    /**
     * Displays widget view in frontend
     *
     * @param array $model
     * @return void
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    public function render(array $model)
    {
        // phpcs:enable

        if (empty($model)) {
            return;
        }

        $title = $this->title(
            $model['before_title'] ?? '',
            $model['title'] ?? '',
            $model['after_title'] ?? ''
        );

        if ($title) {
            echo wp_kses_post($title);
        }

        if (empty($model['items'])) {
            return;
        }

        ?>
            <nav class="mlp-language-switcher-nav">
                <ul>
                    <?php foreach ($model['items'] as $item) :
                        $itemClasses = $this->itemClass($item->siteId());
                        $languageName = (string)apply_filters(self::FILTER_ITEM_LANGUAGE_NAME, $item->languageName());
                        $flagUrl = (string)apply_filters(self::FILTER_ITEM_FLAG_URL, $item->flag(), $item->isoCode());
                        ?>
                        <li class="<?= sanitizeHtmlClass($itemClasses) // phpcs:ignore
                        // WordPress.XSS.EscapeOutput.OutputNotEscaped ?>">
                            <a href="<?= esc_url($item->url()) ?>"
                               class="mlp-language-switcher-item__link"
                               lang="<?= esc_attr($item->locale()) ?>"
                               hreflang="<?= esc_attr($item->locale()) ?>"
                               class="mlp-language-switcher-item__link">
                                <?php if (!empty($model['show_flags']) && $flagUrl) {?>
                                    <img alt="<?= esc_attr($languageName) ?>"
                                         src="<?= esc_url($flagUrl) ?>"
                                    />
                                <?php }?>
                                <?= esc_html($languageName) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php
    }

    /**
     * Creates the widget title markup
     *
     * @param string $beforeTitle
     * @param string $title
     * @param string $afterTitle
     * @return string Tittle markup
     */
    protected function title(string $beforeTitle, string $title, string $afterTitle): string
    {
        if (!$beforeTitle || !$afterTitle) {
            return $title;
        }

        return "{$beforeTitle}{$title}{$afterTitle}";
    }

    /**
     * retrieve an array of item classes
     *
     * @param int $siteId
     * @return array of classes
     */
    protected function itemClass(int $siteId): array
    {
        $currentSiteId = get_current_blog_id();
        $itemClass = ['mlp-language-switcher-item'];

        if ($siteId === $currentSiteId) {
            $itemClass[] = 'mlp-language-switcher-item--current-site';
        }

        return $itemClass;
    }
}
