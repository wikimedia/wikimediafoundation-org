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

namespace Inpsyde\MultilingualPress\Database;

use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository;
use Inpsyde\MultilingualPress\Framework\Cache\Server\Facade;
use Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic;
use Inpsyde\MultilingualPress\Framework\Cache\Server\Server;
use Inpsyde\MultilingualPress\Framework\Database\TableDuplicator;
use Inpsyde\MultilingualPress\Framework\Database\TableInstaller;
use Inpsyde\MultilingualPress\Framework\Database\TableList;
use Inpsyde\MultilingualPress\Framework\Database\TableReplacer;
use Inpsyde\MultilingualPress\Framework\Database\TableStringReplacer;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\ServiceProvider as BaseServiceProvider;

/**
 * Service provider for all database objects.
 */
final class ServiceProvider implements BaseServiceProvider, IntegrationServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $this->registerDbUtils($container);
        $this->registerTables($container);
    }

    /**
     * @param Container $container
     */
    private function registerDbUtils(Container $container)
    {
        $container->shareFactory(
            \wpdb::class,
            static function (): \wpdb {
                return $GLOBALS['wpdb'];
            }
        );

        $container->share(
            TableDuplicator::class,
            static function (Container $container): TableDuplicator {
                return new TableDuplicator($container[\wpdb::class]);
            }
        );

        $container->share(
            TableInstaller::class,
            static function (Container $container): TableInstaller {
                return new TableInstaller($container[\wpdb::class]);
            }
        );

        $container->share(
            TableList::class,
            static function (Container $container): TableList {
                return new TableList(
                    $container[\wpdb::class],
                    new Facade($container[Server::class], TableList::class),
                    $container[CacheSettingsRepository::class]
                );
            }
        );

        $container->share(
            TableReplacer::class,
            static function (Container $container): TableReplacer {
                return new TableReplacer($container[\wpdb::class]);
            }
        );

        $container->share(
            TableStringReplacer::class,
            static function (Container $container): TableStringReplacer {
                return new TableStringReplacer($container[\wpdb::class]);
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function integrate(Container $container)
    {
        $this->integrateCache($container);
    }

    /**
     * @param Container $container
     */
    private function registerTables(Container $container)
    {
        $container->share(
            Table\ContentRelationsTable::class,
            static function (Container $container): Table\ContentRelationsTable {
                return new Table\ContentRelationsTable($container[\wpdb::class]->base_prefix);
            }
        );

        $container->share(
            Table\LanguagesTable::class,
            static function (Container $container): Table\LanguagesTable {
                return new Table\LanguagesTable($container[\wpdb::class]->base_prefix);
            }
        );

        $container->share(
            Table\RelationshipsTable::class,
            static function (Container $container): Table\RelationshipsTable {
                return new Table\RelationshipsTable($container[\wpdb::class]->base_prefix);
            }
        );

        $container->share(
            Table\SiteRelationsTable::class,
            static function (Container $container): Table\SiteRelationsTable {
                return new Table\SiteRelationsTable($container[\wpdb::class]->base_prefix);
            }
        );
    }

    /**
     * @param Container $container
     */
    private function integrateCache(Container $container)
    {
        $tableListCacheLogic = new ItemLogic(TableList::class, TableList::ALL_TABLES_CACHE_KEY);
        $tableListCacheLogic->updateWith([
            $container[TableList::class],
            TableList::ALL_TABLES_CACHE_KEY,
        ]);

        $container[Server::class]->registerForNetwork($tableListCacheLogic);
    }
}
