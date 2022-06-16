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

namespace Inpsyde\MultilingualPress\Module\LanguageManager;

use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Database\Table;
use Inpsyde\MultilingualPress\Framework\Factory\LanguageFactory;
use Inpsyde\MultilingualPress\Framework\Language\Language;

use function Inpsyde\MultilingualPress\settingsErrors;

/**
 * MultilingualPress Language Manager Updater
 */
class Updater
{
    /**
     * @var Db
     */
    private $storage;

    /**
     * @var Table
     */
    private $table;

    /**
     * @var LanguageInstaller
     */
    private $languageInstaller;

    /**
     * @var LanguageFactory
     */
    private $languageFactory;

    /**
     * Updater constructor.
     * @param Db $storage
     * @param Table $table
     * @param LanguageFactory $languageFactory
     * @param LanguageInstaller $languageInstaller
     */
    public function __construct(
        Db $storage,
        Table $table,
        LanguageFactory $languageFactory,
        LanguageInstaller $languageInstaller
    ) {

        $this->storage = $storage;
        $this->table = $table;
        $this->languageFactory = $languageFactory;
        $this->languageInstaller = $languageInstaller;
    }

    /**
     * @param array $languages
     * @return bool
     * @throws NonexistentTable
     */
    public function updateLanguages(array $languages): bool
    {
        $this->excludeEmptyLanguageData($languages);
        $this->excludeMalformedLanguageData($languages);
        $this->ensureKeys($languages);

        if (!$languages) {
            return false;
        }

        $splittedLanguages = $this->splitLanguages($languages);

        $errors = array_merge(
            $this->storage->create($splittedLanguages['toInsert']),
            $this->storage->update($splittedLanguages['toUpdate']),
            $this->storage->delete($splittedLanguages['toDelete'])
        );

        settingsErrors($errors, 'language-manager', 'error');

        !$errors and $this->installLanguages(
            $splittedLanguages['toInsert'] + $splittedLanguages['toUpdate']
        );

        return true;
    }

    /**
     * @param array $languages
     * @return array
     */
    public function splitLanguages(array $languages): array
    {
        $splittedLanguages = [
            'toInsert' => [],
            'toUpdate' => [],
            'toDelete' => [],
        ];
        $indexes = array_map(
            static function (Language $item): int {
                return $item->id();
            },
            $this->storage->read()
        );

        foreach ($indexes as $index) {
            $isNegative = array_key_exists(0 - $index, $languages);
            $index = $isNegative ? 0 - $index : $index;
            $current = $languages[$index] ?? null;

            if (!$current) {
                continue;
            }

            if ($isNegative) {
                $splittedLanguages['toDelete'][absint($index)] = $current;
                continue;
            }

            $splittedLanguages['toUpdate'][$index] = $current;
        }

        foreach ($languages as $index => $item) {
            if (
                $index > 0
                && !array_key_exists($index, $splittedLanguages['toDelete'])
                && !array_key_exists($index, $splittedLanguages['toUpdate'])
            ) {
                $splittedLanguages['toInsert'][$index] = $item;
            }
        }

        return $splittedLanguages;
    }

    /**
     * @param array $languages
     */
    // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
    private function excludeEmptyLanguageData(array &$languages)
    {
        // phpcs:enable
        foreach ($languages as &$language) {
            if (!array_filter($language)) {
                $language = [];
            }
        }

        $languages = array_filter($languages);
    }

    /**
     * @param array $languages
     */
    // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
    private function excludeMalformedLanguageData(array &$languages)
    {
        // phpcs:enable
        foreach ($languages as &$language) {
            if (
                !$language['native_name']
                || !$language['english_name']
                || !$language['locale']
            ) {
                $language = [];
            }
        }

        $languages = array_filter($languages);
    }

    /**
     * @param array $languages
     */
    // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
    private function ensureKeys(array &$languages)
    {
        // phpcs enable
        $tableColumns = $this->table->schema();
        unset(
            $tableColumns['ID'],
            $tableColumns['custom_name']
        );

        foreach ($languages as $key => $language) {
            if (array_diff(array_keys($language), array_keys($tableColumns))) {
                $languages = [];
                break;
            }
        }

        $languages = array_filter($languages);
    }

    /**
     * @param array $languages
     * @return array
     */
    private function installLanguages(array $languages): array
    {
        $response = [];

        foreach ($languages as $language) {
            if (!array_key_exists('locale', $language)) {
                continue;
            }

            $response[$language['locale']] = $this->languageInstaller->install(
                $this->languageFactory->create([$language])
            );
        }

        return $response;
    }
}
