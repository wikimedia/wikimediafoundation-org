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

namespace Inpsyde\MultilingualPress\Framework\Admin;

use function Inpsyde\MultilingualPress\wpHookProxy;

/**
 * Model for a custom column in the Sites list table in the Network Admin.
 */
class SitesListTableColumn
{
    /**
     * @var string
     */
    private $columnName;

    /**
     * @var string
     */
    private $columnLabel;

    /**
     * @var callable
     */
    private $renderCallback;

    /**
     * @param string $columnName
     * @param string $columnLabel
     * @param callable $renderCallback
     */
    public function __construct(
        string $columnName,
        string $columnLabel,
        callable $renderCallback
    ) {

        $this->columnName = $columnName;
        $this->columnLabel = $columnLabel;
        $this->renderCallback = $renderCallback;
    }

    /**
     * Registers the column methods by using the appropriate WordPress hooks.
     */
    public function register()
    {
        add_filter('wpmu_blogs_columns', wpHookProxy(
            function (array $columns): array {
                $columns = array_merge($columns, [$this->columnName => $this->columnLabel]);
                return $columns;
            }
        ));

        add_action(
            'manage_sites_custom_column',
            wpHookProxy(
            // phpcs:ignore Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
                function (string $column, $siteId) {
                    // WordPress pass the $siteId as string not integer
                    // see wp-admin/includes/class-wp-ms-sites-list-table.php:448
                    $siteId = (int)$siteId;
                    $siteId and $this->renderContent($column, $siteId);
                }
            ),
            10,
            2
        );
    }

    /**
     * Renders the column content.
     *
     * @param string $column
     * @param int $siteId
     * @return void
     */
    public function renderContent(string $column, int $siteId)
    {
        if ($column === $this->columnName) {
            echo wp_kses_post(($this->renderCallback)($column, $siteId));
        }
    }
}
