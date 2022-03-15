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

use Inpsyde\MultilingualPress\Core\TaxonomyRepository;
use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\Database\Table;
use Inpsyde\MultilingualPress\Framework\Database\TableInstaller;
use Inpsyde\MultilingualPress\Framework\PluginProperties;
use Inpsyde\MultilingualPress\Framework\SemanticVersionNumber;
use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository;
use Inpsyde\MultilingualPress\Database\Table\ContentRelationsTable;
use Inpsyde\MultilingualPress\Database\Table\LanguagesTable;
use Inpsyde\MultilingualPress\Database\Table\RelationshipsTable;
use Inpsyde\MultilingualPress\Database\Table\SiteRelationsTable;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider;

use function Inpsyde\MultilingualPress\siteLanguageTag;

/**
 * Service provider for all Installation objects.
 */
final class ServiceProvider implements IntegrationServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $this->registerCheckers($container);
        $this->registerInstallers($container);
    }

    /**
     * @inheritdoc
     */
    public function integrate(Container $container)
    {
        add_action(
            SystemChecker::ACTION_CHECKED_VERSION,
            function (int $status, SemanticVersionNumber $installedVersion) use ($container) {
                static $done;
                if (!$done) {
                    $done = true;
                    $this->doInstallOrUpdate($status, $installedVersion, $container);
                }
            },
            10,
            2
        );

        add_action(
            'upgrader_process_complete',
            [$container[Updater::class], 'rewriteRulesAfterPluginUpgrade'],
            10,
            2
        );
    }

    /**
     * @param Container $container
     * @throws \Inpsyde\MultilingualPress\Framework\Service\Exception\NameOverwriteNotAllowed
     * @throws \Inpsyde\MultilingualPress\Framework\Service\Exception\WriteAccessOnLockedContainer
     */
    private function registerCheckers(Container $container)
    {
        $container->addService(
            InstallationChecker::class,
            static function (Container $container): InstallationChecker {
                return new InstallationChecker(
                    $container[SystemChecker::class],
                    $container[PluginProperties::class]
                );
            }
        );

        $container->addService(
            SystemChecker::class,
            static function (Container $container): SystemChecker {
                return new SystemChecker(
                    $container[PluginProperties::class],
                    $container[SiteRelationsChecker::class],
                    $container[SiteSettingsRepository::class]
                );
            }
        );

        $container->addService(
            SiteRelationsChecker::class,
            static function (Container $container): SiteRelationsChecker {
                return new SiteRelationsChecker($container[SiteRelations::class]);
            }
        );
    }

    /**
     * @param Container $container
     * @throws \Inpsyde\MultilingualPress\Framework\Service\Exception\NameOverwriteNotAllowed
     * @throws \Inpsyde\MultilingualPress\Framework\Service\Exception\WriteAccessOnLockedContainer
     */
    private function registerInstallers(Container $container)
    {
        $container->addService(
            Installer::class,
            static function (Container $container): Installer {
                return new Installer($container[TableInstaller::class]);
            }
        );

        $container->addService(
            Updater::class,
            static function (Container $container): Updater {
                return new Updater($container[PluginProperties::class]);
            }
        );

        $container->share(
            NetworkPluginDeactivator::class,
            static function (): NetworkPluginDeactivator {
                return new NetworkPluginDeactivator();
            }
        );

        $container->share(
            Uninstaller::class,
            static function (Container $container): Uninstaller {
                return new Uninstaller($container[TableInstaller::class]);
            }
        );
    }

    /**
     * @param int $status
     * @param SemanticVersionNumber $installedVersion
     * @param Container $container
     */
    private function doInstallOrUpdate(
        int $status,
        SemanticVersionNumber $installedVersion,
        Container $container
    ) {

        remove_all_actions(SystemChecker::ACTION_CHECKED_VERSION);

        switch ($status) {
            case SystemChecker::NEEDS_INSTALLATION:
                $this->doInstall(
                    $container[Installer::class],
                    $container[ContentRelationsTable::class],
                    $container[LanguagesTable::class],
                    $container[RelationshipsTable::class],
                    $container[SiteRelationsTable::class]
                );
                $this->insertSitesLanguages(
                    $container[SiteSettingsRepository::class]
                );
                break;
            case SystemChecker::NEEDS_UPGRADE:
                $this->doUpdate(
                    $container[NetworkPluginDeactivator::class],
                    $container[Updater::class],
                    $installedVersion
                );
                break;
        }

        $this->enableSupportForDefaultTaxonomies(
            $container[TaxonomyRepository::class]
        );
    }

    /**
     * @param Installer $installer
     * @param Table[] ...$tables
     */
    private function doInstall(Installer $installer, Table ...$tables)
    {
        $installer->installTables(...$tables);
    }

    /**
     * @param NetworkPluginDeactivator $deactivator
     * @param Updater $updater
     * @param SemanticVersionNumber $installedVersion
     */
    private function doUpdate(
        NetworkPluginDeactivator $deactivator,
        Updater $updater,
        SemanticVersionNumber $installedVersion
    ) {

        $deactivator->deactivatePlugins('disable-acf.php', 'mlp-wp-seo-compat.php');
        $updater->update($installedVersion);
    }

    /**
     * Update all exists sites language if language for mlp isn't set
     *
     * @param SiteSettingsRepository $repository
     *
     * @return void
     */
    private function insertSitesLanguages(SiteSettingsRepository $repository)
    {
        $sites = get_sites([
            'fields' => 'ids',
        ]);

        foreach ($sites as $siteId) {
            $siteLang = str_replace('_', '-', get_blog_option($siteId, 'WPLANG') ?: 'en-US');
            $siteLanguageTag = siteLanguageTag($siteId);
            !$siteLanguageTag and $repository->updateLanguage($siteLang, $siteId);
        }
    }

    /**
     * When plugin is installed, the support for default taxonomies should be enabled automatically
     *
     * @param TaxonomyRepository $repository
     * @return void
     */
    protected function enableSupportForDefaultTaxonomies(TaxonomyRepository $repository): void
    {
        list($found, $settings) = $repository->allSettings();
        if ($found) {
            return;
        }

        $activeDefaultTaxonomies = array_fill_keys($repository::DEFAULT_SUPPORTED_TAXONOMIES, [$repository::FIELD_ACTIVE => true]);
        $repository->supportTaxonomies($activeDefaultTaxonomies);
    }
}
