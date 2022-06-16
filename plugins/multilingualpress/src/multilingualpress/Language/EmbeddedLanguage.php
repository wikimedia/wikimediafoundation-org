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

namespace Inpsyde\MultilingualPress\Language;

use Inpsyde\MultilingualPress\Database\Table\LanguagesTable;
use Inpsyde\MultilingualPress\Framework\Language\Language as FrameworkLanguage;
use Inpsyde\MultilingualPress\Framework\Language\NullLanguage;

use function Inpsyde\MultilingualPress\allDefaultLanguages;

/**
 * Language wrapping data shipped with MLP.
 */
final class EmbeddedLanguage implements FrameworkLanguage
{
    const KEY_BCP_47_TAG = 'bcp47';
    const KEY_CODE = 'code';
    const KEY_ALT_CODE = 'alt-code';
    const KEY_ISO_639_1 = 'iso-639-1';
    const KEY_ISO_639_2 = 'iso-639-2';
    const KEY_ISO_639_3 = 'iso-639-3';
    const KEY_ISO_NAME = 'iso-name';
    const KEY_LANGUAGE = 'language';
    const KEY_NATIVE_NAME = 'native-name';
    const KEY_ENGLISH_NAME = 'english-name';
    const KEY_RTL = 'rtl';
    const KEY_TYPE = 'type';
    const TYPE_LANGUAGE = 'language';
    const TYPE_LOCALE = 'locale';
    const TYPE_VARIANT = 'variant';

    /**
     * @var Language
     */
    private $language;

    /**
     * @var string
     */
    private $isoName;

    /**
     * @var string
     */
    private $type = '';

    /**
     * @var string
     */
    private $parentLanguageCode = '';

    /**
     * @param array $jsonData
     * @return EmbeddedLanguage
     */
    public static function fromJsonData(array $jsonData): FrameworkLanguage
    {
        $type = $jsonData[self::KEY_TYPE] ?? '';
        if (!in_array($type, [self::TYPE_LOCALE, self::TYPE_LANGUAGE, self::TYPE_VARIANT], true)) {
            return new static(new NullLanguage());
        }

        $isoData = $type === self::TYPE_LANGUAGE
            ? $jsonData
            : ($jsonData[self::KEY_LANGUAGE] ?? []);

        $locale = $jsonData[self::KEY_ALT_CODE] ?? $jsonData[self::KEY_CODE] ?? '';

        if (substr_count($locale, '_') > 1 && $type !== self::TYPE_VARIANT) {
            $localeParts = explode('_', $locale);
            $locale = $localeParts[0] ?? '';
            $locale .= $localeParts[2] ?? '';
        }

        $data = [
            LanguagesTable::COLUMN_BCP_47_TAG => $jsonData[self::KEY_BCP_47_TAG] ?? '',
            LanguagesTable::COLUMN_LOCALE => $locale,
            LanguagesTable::COLUMN_ENGLISH_NAME => $jsonData[self::KEY_ENGLISH_NAME] ?? '',
            LanguagesTable::COLUMN_NATIVE_NAME => $jsonData[self::KEY_NATIVE_NAME] ?? '',
            LanguagesTable::COLUMN_CUSTOM_NAME => '',
            LanguagesTable::COLUMN_RTL => $jsonData[self::KEY_RTL] ?? false,
        ];

        $data[LanguagesTable::COLUMN_ISO_639_1_CODE] = $isoData[self::KEY_ISO_639_1] ?? '';
        $data[LanguagesTable::COLUMN_ISO_639_2_CODE] = $isoData[self::KEY_ISO_639_2] ?? '';
        $data[LanguagesTable::COLUMN_ISO_639_3_CODE] = $isoData[self::KEY_ISO_639_3] ?? '';

        $instance = new static(new \Inpsyde\MultilingualPress\Language\Language($data));
        $instance->type = $type;
        $instance->isoName = $isoData[self::KEY_ISO_NAME] ?? '';
        $instance->parentLanguageCode = $isoData[self::KEY_CODE] ?? '';

        return $instance;
    }

    /**
     * @param Language $language
     */
    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    /**
     * @inheritdoc
     */
    public function id(): int
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function isRtl(): bool
    {
        return $this->language->isRtl();
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return $this->language->name() ?: $this->isoName;
    }

    /**
     * @inheritdoc
     */
    public function englishName(): string
    {
        return $this->language->englishName() ?: $this->isoName;
    }

    /**
     * @inheritdoc
     */
    public function nativeName(): string
    {
        return $this->language->nativeName();
    }

    /**
     * @inheritdoc
     */
    public function isoName(): string
    {
        return $this->isoName;
    }

    /**
     * @inheritdoc
     */
    public function isoCode(string $which = self::ISO_SHORTEST): string
    {
        return $this->language->isoCode($which);
    }

    /**
     * @inheritdoc
     */
    public function bcp47tag(): string
    {
        return $this->language->bcp47tag();
    }

    /**
     * @inheritdoc
     */
    public function locale(): string
    {
        return $this->language->locale();
    }

    public function type(): string
    {
        return $this->type;
    }

    public function parentLanguageTag(): string
    {
        return $this->parentLanguageCode;
    }

    /**
     * The method will change the language variant locale from lang_LANG_Variant to lang_LANG
     *
     * @param string $locale of the language variant
     * @return string changed locale for language variant
     */
    public static function changeLanguageVariantLocale(string $locale): string
    {
        $defaultLanguages = allDefaultLanguages();
        if (
            empty($defaultLanguages[$locale]) ||
            $defaultLanguages[$locale]->type() !== self::TYPE_VARIANT
        ) {
            return $locale;
        }
        $locale = $defaultLanguages[$locale]->locale();
        $localeParts = explode('_', $locale);
        $locale = $localeParts[0] . '-' . $localeParts[1];

        return $locale;
    }

    /**
     * The method will change the language variant from lang-LANG-Variant to lang-LANG
     *
     * @param string $language of the language variant
     * @return string changed language
     */
    public static function changeLanguageVariant(string $language): string
    {
        $defaultLanguages = allDefaultLanguages();
        if (
            empty($defaultLanguages[$language]) ||
            $defaultLanguages[$language]->type() !== self::TYPE_VARIANT
        ) {
            return $language;
        }

        $languageParts = explode('-', $language);
        $language = $languageParts[0] . '-' . $languageParts[1];

        return $language;
    }
}
