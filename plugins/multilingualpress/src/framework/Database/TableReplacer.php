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
 * Table replacer implementations using the WordPress database object.
 */
class TableReplacer
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
     * Replaces the content of one table with another table's content.
     *
     * @param string $destination
     * @param string $source
     * @return bool
     */
    public function replace(string $destination, string $source): bool
    {
        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $hasPrimaryKey = $this->wpdb->get_results(
            "SHOW KEYS FROM {$destination} WHERE Key_name = 'PRIMARY'"
        );

        if ($hasPrimaryKey) {
            $this->wpdb->query("ALTER TABLE {$destination} DISABLE KEYS");
        }

        $this->wpdb->query("TRUNCATE TABLE {$destination}");

        $replaced = (bool)$this->wpdb->query("INSERT INTO {$destination} SELECT * FROM {$source}");

        if ($hasPrimaryKey) {
            $this->wpdb->query("ALTER TABLE {$destination} ENABLE KEYS");
        }
        // phpcs:enable

        return $replaced;
    }
}
