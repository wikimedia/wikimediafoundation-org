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

use function Inpsyde\MultilingualPress\languageByTag;

/**
 * Basic language data type implementation.
 */
final class Language implements FrameworkLanguage
{
    const DEFAULTS = [
        LanguagesTable::COLUMN_ID => 0,
        LanguagesTable::COLUMN_BCP_47_TAG => '',
        LanguagesTable::COLUMN_LOCALE => '',
        LanguagesTable::COLUMN_ISO_639_1_CODE => '',
        LanguagesTable::COLUMN_ISO_639_2_CODE => '',
        LanguagesTable::COLUMN_ISO_639_3_CODE => '',
        LanguagesTable::COLUMN_ENGLISH_NAME => '',
        LanguagesTable::COLUMN_NATIVE_NAME => '',
        LanguagesTable::COLUMN_CUSTOM_NAME => '',
        LanguagesTable::COLUMN_RTL => false,
    ];

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $isoName;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = self::DEFAULTS;
        foreach ($data as $key => $value) {
            if ($key === LanguagesTable::COLUMN_ID && is_numeric($value)) {
                $this->data[$key] = (int)$value;
                continue;
            }
            if ($key === LanguagesTable::COLUMN_RTL) {
                $this->data[$key] = (bool)$value;
                continue;
            }
            if (array_key_exists($key, self::DEFAULTS) && $value && is_string($value)) {
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function id(): int
    {
        return (int)($this->data[LanguagesTable::COLUMN_ID] ?? 0);
    }

    /**
     * @inheritdoc
     */
    public function isRtl(): bool
    {
        return (bool)($this->data[LanguagesTable::COLUMN_RTL] ?? false);
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return $this->nativeName() ?: $this->englishName();
    }

    /**
     * @inheritdoc
     */
    public function englishName(): string
    {
        return (string)($this->data[LanguagesTable::COLUMN_ENGLISH_NAME] ?? '');
    }

    /**
     * @inheritdoc
     */
    public function nativeName(): string
    {
        return (string)($this->data[LanguagesTable::COLUMN_NATIVE_NAME] ?? '');
    }

    /**
     * Returns the language name.
     *
     * @return string
     */
    public function isoName(): string
    {
        $custom = (string)($this->data[LanguagesTable::COLUMN_ENGLISH_NAME] ?? '');
        if ($custom) {
            $this->isoName = $custom;

            return $this->isoName;
        }

        if (is_string($this->isoName)) {
            return $this->isoName;
        }

        if (strlen($this->isoCode()) < 3) {
            $this->isoName = $this->englishName();

            return $this->isoName;
        }

        $lang = languageByTag(strtok($this->bcp47tag(), '-'));
        $name = $lang->englishName();
        if ($name) {
            $this->isoName = $name;

            return $this->isoName;
        }

        $english = $this->englishName();
        $english and $english = trim(strtok($english, '('));

        $this->isoName = $english;

        return $this->isoName;
    }

    /**
     * @inheritdoc
     */
    public function isoCode(string $which = self::ISO_SHORTEST): string
    {
        static $codes;
        $codes or $codes = [
            LanguagesTable::COLUMN_ISO_639_1_CODE,
            LanguagesTable::COLUMN_ISO_639_3_CODE,
            LanguagesTable::COLUMN_ISO_639_2_CODE,
        ];

        if (in_array($which, $codes, true)) {
            return (string)($this->data[$which] ?? '');
        }

        if ($which !== self::ISO_SHORTEST) {
            return '';
        }

        foreach ($codes as $code) {
            if (!empty($this->data[$code])) {
                return (string)$this->data[$code];
            }
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    public function bcp47tag(): string
    {
        return (string)($this->data[LanguagesTable::COLUMN_BCP_47_TAG] ?? '');
    }

    /**
     * @inheritdoc
     */
    public function locale(): string
    {
        return (string)($this->data[LanguagesTable::COLUMN_LOCALE] ?? '');
    }
}
