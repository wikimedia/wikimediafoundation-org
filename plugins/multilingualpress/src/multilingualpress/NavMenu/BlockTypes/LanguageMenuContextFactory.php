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

namespace Inpsyde\MultilingualPress\NavMenu\BlockTypes;

use Inpsyde\MultilingualPress\Api\Translations;
use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\WordpressContext;
use Inpsyde\MultilingualPress\Module\Blocks\Context\ContextFactoryInterface;
use Inpsyde\MultilingualPress\SiteFlags\Flag\Factory as FlagFactory;
use RuntimeException;

use function Inpsyde\MultilingualPress\siteNameWithLanguage;

/**
 * @psalm-type flagDisplayTypeValues = 'only_language'|'flag_and_text'|'only_flag'
 * @psalm-type siteId = int
 * @psalm-type languageInfo = array{name: string, url: string, flagUrl: string}
 * @psalm-type siteLanguage = <siteId, languageInfo>
 * @psalm-type languageMenuContext = array{languages: list<siteLanguage>, flagDisplayType: flagDisplayTypeValues}
 */
class LanguageMenuContextFactory implements ContextFactoryInterface
{
    /**
     * @var Translations
     */
    protected $translations;

    /**
     * @var FlagFactory
     */
    protected $flagFactory;

    public function __construct(Translations $translations, FlagFactory $flagFactory)
    {
        $this->translations = $translations;
        $this->flagFactory = $flagFactory;
    }

    /**
     * @inheritDoc
     * @psalm-return languageMenuContext The context.
     * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
     */
    public function createContext(array $attributes): array
    {
        // phpcs:enable

        $siteIds = $attributes['languages'] ?? [];
        $titleValues = $attributes['titles'] ?? [];
        $flagDisplayType = $attributes['flagDisplayType'] ?? '';
        $languages = [];

        foreach ($siteIds as $siteId) {
            $siteId = (int)$siteId;
            $translation = $this->siteTranslation($siteId);

            foreach ($titleValues as $value) {
                if (! isset($value['id']) || $siteId !== (int)$value['id']) {
                    continue;
                }

                $titleValue = $value['title'] ?? '';
            }

            $languages[$siteId] = [
                'name' => $titleValue ?? siteNameWithLanguage($siteId),
                'url' => $translation->remoteUrl() ?: get_home_url($translation->remoteSiteId(), '/'),
                'flagUrl' => $this->siteFlagUrl($siteId),
            ];
        }

        return ['languages' => $languages, 'flagDisplayType' => $flagDisplayType];
    }

    /**
     * Returns the flag url of given site.
     *
     * @param int $siteId The site ID.
     * @return string The flag url.
     */
    protected function siteFlagUrl(int $siteId): string
    {
        return $this->flagFactory->create($siteId)->url();
    }

    /**
     * Returns the translation of a given site.
     *
     * @param int $siteId The site ID.
     * @return Translation
     */
    protected function siteTranslation(int $siteId): Translation
    {
        $args = TranslationSearchArgs::forContext(new WordpressContext())
            ->forSiteId(get_current_blog_id())
            ->includeBase();

        $translations = $this->translations->searchTranslations($args);
        $translation = $translations[$siteId] ?? false;

        if (!$translation) {
            throw new RuntimeException("The translation doesn't exist");
        }

        return $translation;
    }
}
