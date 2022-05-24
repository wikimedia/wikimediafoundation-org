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

use Inpsyde\MultilingualPress\Framework\Api\Languages;
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Database\Table;
use Inpsyde\MultilingualPress\Framework\Language\Language;

/**
 * MultilingualPress Language Manager Database
 */
class Db
{
    /**
     * @var array
     */
    private static $dbFormat = [
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d',
    ];

    /**
     * @var array
     */
    private static $dbWhere = [
        '%d',
    ];

    /**
     * @var int
     */
    const PAGE_SIZE = 100;

    /**
     * @var Languages
     */
    private $languages;

    /**
     * @var \wpdb
     */
    private $wpdb;

    /**
     * @var Table
     */
    private $table;

    /**
     * Db constructor.
     * @param \wpdb $wpdb
     * @param Languages $languages
     * @param Table $table
     */
    public function __construct(\wpdb $wpdb, Languages $languages, Table $table)
    {
        $this->wpdb = $wpdb;
        $this->languages = $languages;
        $this->table = $table;
    }

    /**
     * @return int
     */
    public function nextLanguageID(): int
    {
        $query = $this->wpdb->prepare(
            // phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
            //phpcs:disable WordPress.DB.PreparedSQLPlaceholders.QuotedSimplePlaceholder
            "SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_name = '%s' AND table_schema = DATABASE()",
            // phpcs:enable
            [
                $this->table->name(),
            ]
        );

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        return (int)$this->wpdb->get_var($query);
        // phpcs:enable
    }

    /**
     * @return Language[]
     */
    public function read(): array
    {
        $languages = $this->languages->allLanguages();

        return $languages;
    }

    /**
     * @param array $items
     * @throws NonexistentTable
     * @return array
     */
    public function update(array $items): array
    {
        if (!$this->table->exists()) {
            throw new NonexistentTable(__FUNCTION__, $this->table->name());
        }

        $errors = [];

        foreach ($items as $id => $values) {
            $this->wpdb->update(
                $this->table->name(),
                (array)$values,
                [
                    'ID' => $id,
                ],
                self::$dbFormat,
                self::$dbWhere
            );
            $errors[$id] = $this->wpdb->last_error;
        }

        return array_filter($errors);
    }

    /**
     * @param array $items
     * @return array
     */
    public function create(array $items): array
    {
        $errors = [];

        foreach ($items as $values) {
            if (!$values) {
                continue;
            }

            $this->languages->insertLanguage($values);
            $errors[] = $this->wpdb->last_error;
        }

        return array_filter($errors);
    }

    /**
     * @param array $items
     * @return array
     */
    public function delete(array $items): array
    {
        $errors = [];

        foreach ($items as $index => $values) {
            if (!$values) {
                continue;
            }

            $this->languages->deleteLanguage($index);
            $errors[] = $this->wpdb->last_error;
        }

        return array_filter($errors);
    }
}
