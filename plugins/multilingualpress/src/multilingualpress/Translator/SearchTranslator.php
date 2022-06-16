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

namespace Inpsyde\MultilingualPress\Translator;

use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\Factory\UrlFactory;
use Inpsyde\MultilingualPress\Framework\Translator\Translator;

/**
 * Translator implementation for search requests.
 */
final class SearchTranslator implements Translator
{
    const FILTER_TRANSLATION = 'multilingualpress.filter_search_translation';

    /**
     * @var UrlFactory
     */
    private $urlFactory;

    /**
     * @param UrlFactory $urlFactory
     */
    public function __construct(UrlFactory $urlFactory)
    {
        $this->urlFactory = $urlFactory;
    }

    /**
     * @inheritdoc
     */
    public function translationFor(int $siteId, TranslationSearchArgs $args): Translation
    {
        $translation = new Translation();
        $searchTerm = $args->searchTerm();

        if (!$searchTerm) {
            return $translation;
        }

        /**
         * Filter Translation bypassing the translation
         *
         * @param bool false True to by pass
         * @param Translation $translation
         * @param int $siteId
         * @param TranslationSearchArgs $args
         */
        $filteredTranslation = apply_filters(
            self::FILTER_TRANSLATION,
            false,
            $translation,
            $siteId,
            $args
        );

        if ($filteredTranslation) {
            return $translation;
        }

        switch_to_blog($siteId);

        $url = get_search_link($searchTerm);

        restore_current_blog();

        $url = $this->urlFactory->create([$url]);

        return $translation->withRemoteUrl($url);
    }
}
