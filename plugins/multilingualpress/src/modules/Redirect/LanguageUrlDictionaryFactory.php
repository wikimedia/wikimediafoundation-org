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

namespace Inpsyde\MultilingualPress\Module\Redirect;

use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;

/**
 * Class LanguageUrlDictionaryFactory
 * @package Inpsyde\MultilingualPress\Module\Redirect
 */
class LanguageUrlDictionaryFactory
{
    /**
     * @var LanguageNegotiator
     */
    private $languageNegotiator;

    /**
     * @var TranslationSearchArgs
     */
    private $translationSearchArgs;

    /**
     * RedirectLanguageUrlDictionary constructor.
     * @param TranslationSearchArgs $translationSearchArgs
     * @param LanguageNegotiator $languageNegotiator
     */
    public function __construct(
        TranslationSearchArgs $translationSearchArgs,
        LanguageNegotiator $languageNegotiator
    ) {

        $this->translationSearchArgs = $translationSearchArgs;
        $this->languageNegotiator = $languageNegotiator;
    }

    /**
     * Language Url Dictionary
     *
     * @return array
     */
    public function create(): array
    {
        $args = $this->translationSearchArgs->makeNotStrictSearch();
        $redirectTargets = $this->languageNegotiator->redirectTargets($args);

        if (!$redirectTargets) {
            return [];
        }

        $urls = [];
        foreach ($redirectTargets as $redirectTarget) {
            $urls[$redirectTarget->language()] = $redirectTarget->url();
        }

        return $urls;
    }
}
