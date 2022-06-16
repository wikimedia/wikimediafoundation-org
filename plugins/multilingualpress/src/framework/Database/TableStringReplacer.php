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

/**
 * Table string replacer implementation using the WordPress database object.
 */
class TableStringReplacer
{

    /**
     * @var \wpdb
     */
    private $wpdb;

    /**
     * @param \wpdb $wpdb
     */
    public function __construct(\wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    /**
     * Replaces one string with another all given columns of the given table at once.
     *
     * @param string $table
     * @param string[] $columns
     * @param string $search
     * @param string $replacement
     * @return int
     */
    public function replace(
        string $table,
        array $columns,
        string $search,
        string $replacement
    ): int {

        if (preg_match('|[^a-z0-9_]|i', $table)) {
            return 0;
        }

        $replacements = $this->replacementsSql($columns, $search, $replacement);
        if (!$replacements) {
            return 0;
        }

        $this->wpdb->query('SET autocommit = 0');
        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $affectedRows = (int)$this->wpdb->query("UPDATE {$table} SET {$replacements}");
        // phpcs:enable
        $this->wpdb->query('COMMIT');
        $this->wpdb->query('SET autocommit = 1');

        return $affectedRows;
    }

    /**
     * Returns SQL string for replacing the given string with given replacement in given columns.
     *
     * @param string[] $columns
     * @param string $search
     * @param string $replacement
     * @return string
     */
    private function replacementsSql(array $columns, string $search, string $replacement): string
    {
        $columns = (array)preg_filter('~^[a-zA-Z_][a-zA-Z0-9_]*$~', '$0', $columns);

        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $sql = array_reduce(
            $columns,
            function (string $sql, string $column) use ($search, $replacement): string {
                return $this->wpdb->prepare(
                    "{$sql}\n\t{$column} = REPLACE ({$column},%s,%s),",
                    $search,
                    $replacement
                );
            },
            ''
        );
        // phpcs:enable

        return substr($sql, 0, -1);
    }
}
