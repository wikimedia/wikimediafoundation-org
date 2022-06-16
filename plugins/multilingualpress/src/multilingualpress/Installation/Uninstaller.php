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

namespace Inpsyde\MultilingualPress\Installation;

use Inpsyde\MultilingualPress\Framework\Database\Table;
use Inpsyde\MultilingualPress\Framework\Database\TableInstaller;
use Inpsyde\MultilingualPress\Framework\NetworkState;

/**
 * MultilingualPress uninstaller.
 */
class Uninstaller
{
    /**
     * @var int[]
     */
    private $siteIds;

    /**
     * @var TableInstaller
     */
    private $tableInstaller;

    /**
     * @param TableInstaller $tableInstaller
     */
    public function __construct(TableInstaller $tableInstaller)
    {
        $this->tableInstaller = $tableInstaller;
    }

    /**
     * Uninstalls the given tables.
     *
     * @param Table[] $tables
     * @return int
     */
    public function uninstallTables(array $tables): int
    {
        return (int)array_reduce(
            $tables,
            function (int $uninstalled, Table $table): int {
                return $uninstalled + (int)$this->tableInstaller->uninstall($table);
            },
            0
        );
    }

    /**
     * Deletes all MultilingualPress network options.
     *
     * @param string[] $options
     * @return int
     */
    public function deleteNetworkOptions(array $options): int
    {
        return array_reduce(
            $options,
            static function (int $deleted, string $option): int {
                return $deleted + (int)delete_network_option(0, $option);
            },
            0
        );
    }

    /**
     * Deletes all MultilingualPress post meta.
     *
     * @param string[] $keys
     * @param int[] $siteIds
     * @return bool
     */
    public function deletePostMeta(array $keys, array $siteIds = []): bool
    {
        $siteIds = $siteIds ?: $this->siteIds();
        if (!$siteIds) {
            return false;
        }

        $networkState = NetworkState::create();

        array_walk(
            $siteIds,
            static function (int $siteId) use ($keys) {
                switch_to_blog($siteId);
                array_walk(
                    $keys,
                    static function (string $key) {
                        delete_post_meta_by_key($key);
                    }
                );
            }
        );

        $networkState->restore();

        return true;
    }

    /**
     * Deletes all MultilingualPress options for the given (or all) sites.
     *
     * @param string[] $options
     * @param int[] $siteIds
     * @return int
     */
    public function deleteSiteOptions(array $options, array $siteIds = []): int
    {
        $siteIds or $siteIds = $this->siteIds();
        if (!$siteIds) {
            return 0;
        }

        $networkState = NetworkState::create();

        $deleted = array_reduce(
            $siteIds,
            static function (int $deleted, int $siteId) use ($options): int {

                switch_to_blog($siteId);

                $deleted += array_reduce(
                    $options,
                    static function (int $deleted, string $option): int {
                        return $deleted + (int)delete_option($option);
                    },
                    $deleted
                );

                return $deleted;
            },
            0
        );

        $networkState->restore();

        return $deleted;
    }

    /**
     * Deletes all MultilingualPress user meta.
     *
     * @param string[] $keys
     */
    public function deleteUserMeta(array $keys)
    {
        array_walk(
            $keys,
            static function (string $key) {
                delete_metadata('user', 0, $key, '', true);
            }
        );
    }

    /**
     * @param array $siteOptions
     * @param array $userMeta
     */
    public function deleteOnboardingData(array $siteOptions, array $userMeta)
    {
        foreach ($siteOptions as $option) {
            delete_site_option($option);
        }

        foreach ($userMeta as $meta) {
            delete_user_meta(get_current_user_id(), $meta);
        }
    }

    /**
     * Unschedule all MLP events
     *
     * When the plugin is uninstalled, we need to remove all the scheduled events
     *
     * @param array<string> $events The array of the hook names for which the events should be unscheduled
     */
    public function deleteScheduledEvents(array $events)
    {
        foreach ($events as $event) {
            wp_unschedule_hook($event);
        }
    }

    /**
     * Returns an array with all site IDs.
     *
     * @return int[]
     */
    private function siteIds(): array
    {
        if (!is_array($this->siteIds)) {
            $this->siteIds = wp_parse_id_list(get_sites(['fields' => 'ids']));
        }

        return $this->siteIds;
    }
}
