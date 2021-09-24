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

namespace Inpsyde\MultilingualPress\Api;

use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository;
use Inpsyde\MultilingualPress\Database\Table\LanguagesTable;
use Inpsyde\MultilingualPress\Framework\Api\Languages;
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Database\Table;
use Inpsyde\MultilingualPress\Framework\Language\Language;
use Inpsyde\MultilingualPress\Framework\Factory\LanguageFactory;
use Inpsyde\MultilingualPress\Framework\Language\NullLanguage;

use function Inpsyde\MultilingualPress\allDefaultLanguages;

/**
 * Languages API implementation using the WordPress database object.
 */
final class WpdbLanguages implements Languages
{
    /**
     * @var \wpdb
     */
    private $wpdb;

    /**
     * @var string[]
     */
    private $fields;

    /**
     * @var SiteSettingsRepository
     */
    private $siteSettingsRepository;

    /**
     * @var Table
     */
    private $table;

    /**
     * @var LanguageFactory
     */
    private $languageFactory;

    /**
     * @param \wpdb $wpdb
     * @param Table $table
     * @param SiteSettingsRepository $siteSettingsRepository
     * @param LanguageFactory $languageFactory
     */
    public function __construct(
        \wpdb $wpdb,
        Table $table,
        SiteSettingsRepository $siteSettingsRepository,
        LanguageFactory $languageFactory
    ) {

        $this->wpdb = $wpdb;
        $this->table = $table;
        $this->siteSettingsRepository = $siteSettingsRepository;
        $this->fields = $this->extractFieldSpecificationsFromTable($table);
        $this->languageFactory = $languageFactory;
    }

    /**
     * @inheritdoc
     */
    public function deleteLanguage(int $id): bool
    {
        if (!$this->table->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->table->name());
        }

        $dbDelete = $this->wpdb->delete(
            $this->table->name(),
            [LanguagesTable::COLUMN_ID => $id],
            '%d'
        );

        return $dbDelete !== false;
    }

    /**
     * @inheritdoc
     */
    public function allLanguages(): array
    {
        if (!$this->table->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->table->name());
        }

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        $orderCol = LanguagesTable::COLUMN_ENGLISH_NAME;
        $query = "SELECT * FROM {$this->table->name()} ORDER BY {$orderCol} ASC";
        $results = $this->wpdb->get_results($query, ARRAY_A);
        // phpcs:enable
        if (!$results || !is_array($results)) {
            return [];
        }
        return array_map([$this, 'languageFromData'], $results);
    }

    /**
     * @inheritdoc
     */
    public function allAssignedLanguages(): array
    {
        if (!$this->table->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->table->name());
        }

        $languages = $this->siteSettingsRepository->allSitesSetting(
            SiteSettingsRepository::KEY_LANGUAGE
        );

        if (!$languages) {
            return [];
        }

        $whereFormat = rtrim(str_repeat('%s,', count($languages)), ',');
        $column = LanguagesTable::COLUMN_BCP_47_TAG;

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $query = $this->wpdb->prepare(
            "SELECT * FROM {$this->table->name()} WHERE {$column} IN ($whereFormat)",
            $languages
        );

        $dbLanguages = (array)$this->wpdb->get_results($query, ARRAY_A);
        // phpcs:enable

        $result = [];
        $toFind = $languages;
        foreach ($dbLanguages as $dbLanguage) {
            $tag = $dbLanguage[LanguagesTable::COLUMN_BCP_47_TAG];
            $key = array_search($tag, $languages, true);
            if (is_numeric($key) && $key) {
                $result[(int)$key] = $this->languageFromData($dbLanguage);
                unset($toFind[$key]);
            }
        }

        $defaultLanguages = $toFind ? allDefaultLanguages() : [];
        foreach ($toFind as $siteId => $tag) {
            if (array_key_exists($tag, $defaultLanguages)) {
                $result[$siteId] = $defaultLanguages[$tag];
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function languageBy(string $column, $value): Language
    {
        if (!$this->table->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->table->name());
        }

        // phpcs:enable
        static $queryableColumns;
        $queryableColumns or $queryableColumns = [
            LanguagesTable::COLUMN_BCP_47_TAG,
            LanguagesTable::COLUMN_LOCALE,
            LanguagesTable::COLUMN_ENGLISH_NAME,
        ];

        if (false === array_search($column, $queryableColumns, true)) {
            return new NullLanguage();
        }

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $language = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table->name()} WHERE {$column} = %s LIMIT 1",
                $value
            ),
            ARRAY_A
        );
        // phpcs:enable

        if (!$language || !is_array($language)) {
            return new NullLanguage();
        }

        return $this->languageFactory->create([$language]);
    }

    /**
     * @inheritdoc
     */
    public function insertLanguage(array $languageData): int
    {
        if (
            !empty($languageData[LanguagesTable::COLUMN_ID])
            && is_numeric($languageData[LanguagesTable::COLUMN_ID])
        ) {
            $update = $this->updateLanguage(
                (int)$languageData[LanguagesTable::COLUMN_ID],
                $languageData
            );

            return $update ? (int)$languageData[LanguagesTable::COLUMN_ID] : 0;
        }

        $languageData = $this->ensureRequiredData($languageData);
        if (!$languageData) {
            return 0;
        }

        $languageData = array_intersect_key($languageData, $this->fields);
        unset($languageData[LanguagesTable::COLUMN_ID]);

        $languageId = $this->languageIdFromData($languageData);

        if ($languageId) {
            return $this->updateLanguage($languageId, $languageData) ? $languageId : 0;
        }

        $dbInsert = $this->wpdb->insert(
            $this->table->name(),
            $languageData,
            $this->fieldSpecifications($languageData)
        );

        return $dbInsert ? (int)$this->wpdb->insert_id : 0;
    }

    /**
     * @inheritdoc
     */
    public function updateLanguage(int $id, array $languageData): bool
    {
        if (!$this->table->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->table->name());
        }

        $languageData = $this->ensureRequiredData($languageData);
        if (!$languageData || $id < 1 || $this->languageIdFromData($languageData) !== $id) {
            return false;
        }

        $languageData = array_intersect_key($languageData, $this->fields);

        $dbUpdate = $this->wpdb->update(
            $this->table->name(),
            $languageData,
            [LanguagesTable::COLUMN_ID => $id],
            $this->fieldSpecifications($languageData),
            '%d'
        );

        return $dbUpdate !== false;
    }

    /**
     * @param array $languageData
     * @return array
     */
    private function ensureRequiredData(array $languageData): array
    {
        $bcp47 = $languageData[LanguagesTable::COLUMN_BCP_47_TAG] ?? '';
        $locale = $languageData[LanguagesTable::COLUMN_LOCALE] ?? '';
        if (!$bcp47 && !$locale) {
            return [];
        }

        $bcp47 or $bcp47 = str_replace('_', '-', $locale);
        $locale or $locale = str_replace('-', '_', $bcp47);
        if (substr_count($locale, '_') > 1) {
            $localeParts = explode('_', $locale);
            $locale = $localeParts[0] ?? '';
            $locale .= $localeParts[2] ?? '';
        }

        $languageData[LanguagesTable::COLUMN_BCP_47_TAG] = $bcp47;
        $languageData[LanguagesTable::COLUMN_LOCALE] = $locale;

        return $languageData;
    }

    /**
     * Return language ID if the language identified by language data exists already, 0 otherwise.
     *
     * @param array $languageData
     * @return int
     * @throws NonexistentTable
     */
    private function languageIdFromData(array $languageData): int
    {
        if (!$this->table->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->table->name());
        }

        $bcp47 = $languageData[LanguagesTable::COLUMN_BCP_47_TAG] ?? '';
        $locale = $languageData[LanguagesTable::COLUMN_LOCALE] ?? '';
        if (!$bcp47 && !$locale) {
            return 0;
        }

        $selectField = LanguagesTable::COLUMN_ID;
        $whereField = $bcp47 ? LanguagesTable::COLUMN_BCP_47_TAG : LanguagesTable::COLUMN_LOCALE;
        $whereSubject = $bcp47 ?: $locale;

        $query = "SELECT {$selectField} FROM {$this->table->name()} WHERE {$whereField} = %s";
        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        return (int)$this->wpdb->get_var($this->wpdb->prepare($query, $whereSubject));
        // phpcs:enable
    }

    /**
     * Returns a new language object, instantiated with the given data.
     *
     * @param array $data
     * @return Language
     */
    private function languageFromData(array $data): Language
    {
        return $this->languageFactory->create([$data]);
    }

    /**
     * Returns an array with column names as keys and the individual printf specification as value.
     *
     * The're a lot more specifications, but we don't need more than telling a string from an int.
     *
     * @param Table $table
     * @return string[]
     */
    private function extractFieldSpecificationsFromTable(Table $table): array
    {
        $schema = $table->schema();
        $intFields = implode(
            '|',
            [
                'BIT',
                'DECIMAL',
                'DOUBLE',
                'FLOAT',
                'INT',
                'NUMERIC',
                'REAL',
            ]
        );

        $spec = [];
        foreach ($schema as $key => $def) {
            $spec[$key] = preg_match("/^\s*[A-Z]*({$intFields})/i", $def)
                ? '%d'
                : '%s';
        }

        return $spec;
    }

    /**
     * Returns an array with the according specifications for all fields included in the given language.
     *
     * @param array $language
     * @return array
     */
    private function fieldSpecifications(array $language): array
    {
        return array_map(
            function (string $field): string {
                return $this->fields[$field] ?? '%s';
            },
            array_keys($language)
        );
    }
}
