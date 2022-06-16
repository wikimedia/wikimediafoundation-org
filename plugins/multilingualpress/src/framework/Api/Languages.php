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

namespace Inpsyde\MultilingualPress\Framework\Api;

use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Language\Language;

/**
 * Interface for all languages API implementations.
 */
interface Languages
{

    /**
     * Deletes the language with the given ID.
     *
     * @param int $id
     * @return bool
     * @throws NonexistentTable
     */
    public function deleteLanguage(int $id): bool;

    /**
     * Returns an array with objects of all available languages.
     *
     * @return Language[]
     * @throws NonexistentTable
     */
    public function allLanguages(): array;

    /**
     * Returns the complete language data of all sites.
     *
     * @return Language[]
     * @throws NonexistentTable
     */
    public function allAssignedLanguages(): array;

    /**
     * Returns the language for the given arguments.
     *
     * @param string $column
     * @param string|int $value
     * @return Language
     * @throws NonexistentTable
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function languageBy(string $column, $value): Language;

    /**
     * Creates a new language entry according to the given data.
     *
     * @param array $languageData
     * @return int
     * @throws NonexistentTable
     */
    public function insertLanguage(array $languageData): int;

    /**
     * Updates the language with the given ID according to the given data.
     *
     * @param int $id
     * @param array $data
     * @return bool
     * @throws NonexistentTable
     */
    public function updateLanguage(int $id, array $data): bool;
}
