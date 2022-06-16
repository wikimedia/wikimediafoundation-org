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

namespace Inpsyde\MultilingualPress\Attachment;

use Inpsyde\MultilingualPress\Framework\BasePathAdapter;
use Inpsyde\MultilingualPress\Framework\Database\TableStringReplacer;
use Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
use wpdb;

/**
 * Class DataBaseDataReplacer
 * @package Inpsyde\MultilingualPress\Attachment
 */
class DatabaseDataReplacer
{
    use SwitchSiteTrait;

    const FILTER_TABLES = 'multilingualpress.database_data_replacer_tables';

    /**
     * @var TableStringReplacer
     */
    private $tableStringReplacer;

    /**
     * @var wpdb
     */
    private $wpdb;

    /**
     * @var BasePathAdapter
     */
    private $basePathAdapter;

    /**
     * UrlDataBaseReplacer constructor.
     * @param wpdb $wpdb
     * @param TableStringReplacer $tableStringReplacer
     * @param BasePathAdapter $basePathAdapter
     */
    public function __construct(
        wpdb $wpdb,
        TableStringReplacer $tableStringReplacer,
        BasePathAdapter $basePathAdapter
    ) {

        $this->tableStringReplacer = $tableStringReplacer;
        $this->wpdb = $wpdb;
        $this->basePathAdapter = $basePathAdapter;
    }

    /**
     * Updates attachment URLs according to the given arguments.
     *
     * @param int $sourceSiteId
     * @param int $targetSiteId
     */
    public function replaceUrlsForSites(int $sourceSiteId, int $targetSiteId)
    {
        $sourceSiteUrl = $this->basePathAdapter->baseurlForSite($sourceSiteId);
        $destinationSiteUrl = $this->basePathAdapter->baseurlForSite($targetSiteId);

        $previousSiteId = $this->maybeSwitchSite($targetSiteId);

        $tables = [
            $this->wpdb->comments => [
                'comment_content',
            ],
            $this->wpdb->posts => [
                'guid',
                'post_content',
                'post_content_filtered',
                'post_excerpt',
            ],
            $this->wpdb->term_taxonomy => [
                'description',
            ],
        ];

        /**
         * Filter Tables & Columns
         *
         * @param array $tables A list of table names and their columns
         */
        $tables = apply_filters(self::FILTER_TABLES, $tables);

        foreach ($tables as $table => $columns) {
            $this->tableStringReplacer->replace(
                $table,
                $columns,
                $sourceSiteUrl,
                $destinationSiteUrl
            );
        }

        $this->maybeRestoreSite($previousSiteId);
    }
}
