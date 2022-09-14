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

namespace Inpsyde\MultilingualPress\Framework\Database;

/**
 * Interface for all tables.
 */
interface Table
{
    /**
     * Returns an array with all columns that do not have any default content.
     *
     * @return string[]
     */
    public function columnsWithoutDefaultContent(): array;

    /**
     * Returns the SQL string for the default content.
     *
     * @return string
     */
    public function defaultContentSql(): string;

    /**
     * Returns the SQL string for all (unique) keys.
     *
     * @return string
     */
    public function keysSql(): string;

    /**
     * Returns the table name.
     *
     * @return string
     */
    public function name(): string;

    /**
     * Check if table exists or not
     *
     * @return bool
     */
    public function exists(): bool;

    /**
     * Returns the primary key.
     *
     * @return string
     */
    public function primaryKey(): string;

    /**
     * Returns the table schema as an array with column names as keys and SQL definitions as values.
     *
     * @return string[]
     */
    public function schema(): array;
}
