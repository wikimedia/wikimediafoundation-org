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

use Inpsyde\MultilingualPress\Flags\Flag\Flag;
use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Api\Translations;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\WordpressContext;

class Model
{
    /**
     * @var Translations
     */
    private $translations;

    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @param Translations $translations
     * @param ItemFactory $itemFactory
     */
    public function __construct(Translations $translations, ItemFactory $itemFactory)
    {
        $this->translations = $translations;
        $this->itemFactory = $itemFactory;
    }

    /**
     * @param array $args
     * @param array $instance
     * @return array
     */
    public function data(array $args, array $instance): array
    {
        $translations = $this->translations();
        if (!$translations) {
            return [];
        }

        $model['before_title'] = $args['before_title'] ?? '';
        $model['title'] = $instance['title'] ?? '';
        $model['after_title'] = $args['after_title'] ?? '';
        // phpcs:ignore Inpsyde.CodeQuality.LineLength.TooLong
        $model['show_links_for_translated_content_only'] = $instance['show_links_for_translated_content_only'] ?? 0;
        $model['show_current_site'] = $instance['show_current_site'] ?? 0;
        $model['show_flags'] = $instance['show_flags'] ?? 0;
        $model['language_name'] = $instance['language_name'] ?? 'isoName';
        $model['items'] = [];

        foreach ($translations as $translation) {
            if ($translation->remoteSiteId() === get_current_blog_id() && $model['show_current_site'] !== 1) {
                continue;
            }

            $url = $translation->remoteUrl() ?: get_home_url($translation->remoteSiteId(), '/');
            if ($model['show_links_for_translated_content_only'] === 1) {
                $url = $translation->remoteUrl();
            }
            if (!$url) {
                continue;
            }

            $language = $translation->language();

            $model['items'][] = $this->itemFactory->create(
                $language->{$model['language_name']}(),
                $language->bcp47tag(),
                $language->isoCode(),
                $this->languageFlag($model, $language->isoCode()),
                $url,
                $translation->remoteSiteId()
            );
        }

        return $model;
    }

    /**
     * @return Translation[]
     */
    protected function translations(): array
    {
        $args = TranslationSearchArgs::forContext(new WordpressContext())
            ->forSiteId(get_current_blog_id())
            ->includeBase();

        $translations = $this->translations->searchTranslations($args);

        return $translations;
    }

    /**
     * Returns flag image url from multilingualpress-site-flags plugin
     *
     * @param array $model
     * @param string $isoCode
     * @return string
     */
    protected function languageFlag(array $model, string $isoCode): string
    {
        if (interface_exists(Flag::class) && $model['show_flags'] === 1) {
            return plugins_url('multilingualpress-site-flags/', dirname(__DIR__, 3))
                . "resources/images/flags/{$isoCode}.gif";
        }

        return '';
    }
}
