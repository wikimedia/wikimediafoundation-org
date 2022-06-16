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

namespace Inpsyde\MultilingualPress\Framework\Database;

use Inpsyde\MultilingualPress\Framework\Database\Exception\InvalidTable;

/**
 * Table installer implementation using the WordPress database object.
 */
class TableInstaller
{
    /**
     * @var \wpdb
     */
    private $wpdb;

    /**
     * @var string
     */
    private $options;

    /**
     * @var Table
     */
    private $table;

    /**
     * @param \wpdb $wpdb
     * @param Table|null $table
     */
    public function __construct(\wpdb $wpdb, Table $table = null)
    {
        $this->wpdb = $wpdb;
        $this->table = $table;
    }

    /**
     * Installs the given table.
     *
     * @param Table|null $table
     * @return bool
     * @throws InvalidTable If a table was neither passed, nor injected via the constructor.
     */
    public function install(Table $table = null): bool
    {
        $table = $table ?: $this->table;
        if (!$table) {
            throw InvalidTable::forAction('install');
        }

        $tableName = $table->name();
        $schema = $table->schema();
        $columns = $this->allColumns($schema);
        $keys = $this->allKeys($table);
        $options = $this->tableOptions();

        /**
         * WordPress file with the dbDelta() function.
         */
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta("CREATE TABLE {$tableName} ({$columns}{$keys}) {$options}");

        if (!$this->tableExists($tableName)) {
            return false;
        }

        $this->insertDefaultContent($table);

        return true;
    }

    /**
     * Uninstalls the given table.
     *
     * @param Table|null $table
     * @return bool
     * @throws InvalidTable If a table was neither passed, nor injected via the constructor.
     */
    public function uninstall(Table $table = null): bool
    {
        $table = $table ?: $this->table;
        if (!$table) {
            throw InvalidTable::forAction('uninstall');
        }

        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        return false !== $this->wpdb->query('DROP TABLE IF EXISTS ' . $table->name());
        // phpcs:enable
    }

    /**
     * Returns the according SQL string for the columns in the given table schema.
     *
     * @param array $schema
     * @return string
     */
    private function allColumns(array $schema): string
    {
        $sql = '';
        foreach ($schema as $name => $definition) {
            $sql and $sql .= ',';
            $sql .= "\n\t{$name} {$definition}";
        }

        return $sql;
    }

    /**
     * Returns the according SQL string for the keys of the given table.
     *
     * @param Table $table
     * @return string
     */
    private function allKeys(Table $table): string
    {
        $keys = '';
        $primaryKey = $table->primaryKey();
        if ($primaryKey) {
            // Due to dbDelta: two spaces after PRIMARY KEY!
            $keys .= ",\n\tPRIMARY KEY  ({$primaryKey})";
        }

        $keysSql = $table->keysSql();
        if ($keysSql) {
            $keys .= ",\n\t$keysSql";
        }

        return "{$keys}\n";
    }

    /**
     * Returns the SQL string for the table options.
     *
     * @return string
     */
    private function tableOptions(): string
    {
        if (is_string($this->options)) {
            return $this->options;
        }

        $options = 'DEFAULT CHARACTER SET ';
        // MultilingualPress requires multibyte encoding for native names of languages.
        $options .= (empty($this->wpdb->charset) || false === stripos($this->wpdb->charset, 'utf'))
            ? 'utf8'
            : $this->wpdb->charset;

        if (!empty($this->wpdb->collate)) {
            $options .= ' COLLATE ' . $this->wpdb->collate;
        }

        $this->options = $options;

        return $this->options;
    }

    /**
     * Checks if a table with the given name exists in the database.
     *
     * @param string $tableName
     * @return bool
     */
    private function tableExists(string $tableName): bool
    {
        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        $query = $this->wpdb->prepare('SHOW TABLES LIKE %s', $tableName);

        return (bool)$this->wpdb->query($query);
        // phpcs:enable
    }

    /**
     * Inserts the according default content into the given table.
     *
     * @param Table $table
     */
    private function insertDefaultContent(Table $table)
    {
        $tableName = $table->name();
        // Bail if the table is not empty.
        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        if ($this->wpdb->query("SELECT 1 FROM {$tableName} LIMIT 1")) {
            return;
        }

        $defaultContent = $table->defaultContentSql();
        if (!$defaultContent) {
            return;
        }

        $columns = array_keys($table->schema());
        $columns = array_diff($columns, $table->columnsWithoutDefaultContent());
        $columns = implode(',', $columns);

        $this->wpdb->query("INSERT INTO {$tableName} ({$columns}) VALUES {$defaultContent}");
        // phpcs:enable
    }
}
