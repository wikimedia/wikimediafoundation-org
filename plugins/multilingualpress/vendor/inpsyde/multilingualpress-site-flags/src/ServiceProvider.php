<?php

/*
 * This file is part of the MultilingualPress Site Flag package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\SiteFlags;

use Inpsyde\MultilingualPress\Asset\AssetFactory;
use Inpsyde\MultilingualPress\Core\Admin\SiteSettings as ParentSiteSettings;
use Inpsyde\MultilingualPress\Core\Admin\NewSiteSettings as ParentNewSiteSettings;
use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsUpdater as ParentSiteSettingsUpdater;
use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsUpdateRequestHandler as ParentSiteSiteSettingsUpdateRequestHandler;
use Inpsyde\MultilingualPress\SiteFlags\Flag\Factory;
use Inpsyde\MultilingualPress\Core\Locations;
use Inpsyde\MultilingualPress\SiteFlags\Core\Admin\SiteSettingsRepository;
use Inpsyde\MultilingualPress\SiteFlags\Core\Admin\SiteSettingsUpdater;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Factory\NonceFactory;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Module\Exception\ModuleAlreadyRegistered;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\Exception\NameOverwriteNotAllowed;
use Inpsyde\MultilingualPress\Framework\Service\Exception\WriteAccessOnLockedContainer;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingMultiView;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionView;
use Inpsyde\MultilingualPress\SiteFlags\Core\Admin\SiteFlagUrlSetting;
use Inpsyde\MultilingualPress\SiteFlags\Core\Admin\SiteMenuLanguageStyleSetting;
use Inpsyde\MultilingualPress\TranslationUi\Post\TableList;

class ServiceProvider implements ModuleServiceProvider
{
    public const MODULE_ID = 'multilingualpress-site-flags';
    protected const OLD_FLAGS_ADDON_PATH = 'multilingualpress-site-flags/multilingualpress-site-flags.php';

    /**
     * Registers the module at the module manager.
     *
     * @param ModuleManager $moduleManager
     * @return bool
     * @throws ModuleAlreadyRegistered
     */
    public function registerModule(ModuleManager $moduleManager): bool
    {
        $disabledDescription = '';

        if ($this->isSiteFlagsAddonActive()) {
            $disabledDescription = __(
                'The module can be activated only if the old MultilingualPress Site Flags Addon is disabled',
                'multilingualpress'
            );
            $moduleManager->unregisterById(self::MODULE_ID);
        }

        return $moduleManager->register(
            new Module(
                self::MODULE_ID,
                [
                    'description' => "{$this->description()} {$disabledDescription}",
                    'name' => __('MultilingualPress Site Flags', 'multilingualpress'),
                    'active' => false,
                    'disabled' => $this->isSiteFlagsAddonActive(),
                ]
            )
        );
    }

    /**
     * Registers the provided services on the given container.
     *
     * @param Container $container
     * @throws NameOverwriteNotAllowed
     * @throws WriteAccessOnLockedContainer
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    public function register(Container $container)
    {
        // phpcs:enable

        $flagsPath = rtrim(plugins_url('/', dirname(__FILE__))) . 'resources/images/flags';
        $container->shareValue('multilingualpress.siteFlags.flagsPath', $flagsPath);

        $container->share(
            'siteFlagsProperties',
            static function (): array {
                $path = dirname(__FILE__);
                $pluginPath = rtrim(plugin_dir_path($path), '/');
                $pluginUrl = rtrim(plugins_url('/', $path), '/');
                $publicDirName = "public";

                return [
                    'pluginPath' => $pluginPath,
                    'pluginUrl' => $pluginUrl,
                    'assetsPath' => "{$pluginPath}/{$publicDirName}",
                    'assetsUrl' => "{$pluginUrl}/{$publicDirName}",
                ];
            }
        );

        $container->addFactory(
            Factory::class,
            static function () use ($container): Factory {
                return new Factory(
                    $container[SiteSettingsRepository::class]
                );
            }
        );

        $container->addService(
            FlagFilter::class,
            static function (Container $container): FlagFilter {
                return new FlagFilter(
                    $container[SiteSettingsRepository::class],
                    $container[Factory::class],
                    $container->get('multilingualpress.siteFlags.flagsPath')
                );
            }
        );

        $container->share(
            'FlagsLocations',
            static function (Container $container): Locations {
                $properties = $container['siteFlagsProperties'];
                $assetsPath = $properties['assetsPath'];
                $assetsUrl = $properties['assetsUrl'];
                $locations = new Locations();

                return $locations
                    ->add('plugin', $properties['pluginPath'], $properties['pluginUrl'])
                    ->add('css', "{$assetsPath}/css", "{$assetsUrl}/css")
                    ->add('js', "{$assetsPath}/js", "{$assetsUrl}/js");
            }
        );

        $container->share(
            SiteSettingsRepository::class,
            static function (): SiteSettingsRepository {
                return new SiteSettingsRepository();
            }
        );

        $container->addService(
            SiteFlagUrlSetting::class,
            static function (Container $container): SiteFlagUrlSetting {
                return new SiteFlagUrlSetting(
                    $container[SiteSettingsRepository::class]
                );
            }
        );

        $container->addService(
            SiteMenuLanguageStyleSetting::class,
            static function (Container $container): SiteMenuLanguageStyleSetting {
                return new SiteMenuLanguageStyleSetting(
                    $container[SiteSettingsRepository::class]
                );
            }
        );

        $container->addService(
            'FlagsSiteSettings',
            static function (Container $container): ParentSiteSettings {
                return new ParentSiteSettings(
                    SiteSettingMultiView::fromViewModels(
                        [
                            $container[SiteFlagUrlSetting::class],
                            $container[SiteMenuLanguageStyleSetting::class],
                        ]
                    ),
                    $container[AssetManager::class]
                );
            }
        );

        $container->addService(
            'FlagsNewSiteSettings',
            static function (Container $container): ParentNewSiteSettings {
                return new ParentNewSiteSettings(
                    SiteSettingMultiView::fromViewModels(
                        [
                            $container[SiteFlagUrlSetting::class],
                            $container[SiteMenuLanguageStyleSetting::class],
                        ]
                    )
                );
            }
        );

        $container->addService(
            SiteSettingsUpdater::class,
            static function (Container $container): SiteSettingsUpdater {
                return new SiteSettingsUpdater(
                    $container[SiteSettingsRepository::class],
                    $container[ServerRequest::class]
                );
            }
        );

        $container->addService(
            'FlagSiteSettingsUpdateHandler',
            static function (Container $container): ParentSiteSiteSettingsUpdateRequestHandler {
                return new ParentSiteSiteSettingsUpdateRequestHandler(
                    $container[SiteSettingsUpdater::class],
                    $container[ServerRequest::class],
                    $container[NonceFactory::class]->create(['save_site_settings'])
                );
            }
        );

        $container->share(
            'FlagsAssetFactory',
            static function (Container $container): AssetFactory {
                return new AssetFactory($container['FlagsLocations']);
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function activateModule(Container $container)
    {
        if (is_admin()) {
            $this->bootstrapAdmin($container);
            is_network_admin() and $this->bootstrapNetworkAdmin($container);

            return;
        }

        $this->bootstrapFrontend($container);
    }

    /**
     * @param Container $container
     */
    public function bootstrapAdmin(Container $container)
    {
        $flagSiteSettingsUpdateHandler = $container['FlagSiteSettingsUpdateHandler'];

        add_action(SiteSettingsSectionView::ACTION_AFTER . '_mlp-site-settings', [
            $container['FlagsSiteSettings'],
            'renderView',
        ]);

        add_action(
            ParentSiteSettingsUpdater::ACTION_UPDATE_SETTINGS,
            static function () use ($flagSiteSettingsUpdateHandler) {
                $flagSiteSettingsUpdateHandler->handlePostRequest();
            },
            20
        );

        $assetFactory = $container['FlagsAssetFactory'];

        $container[AssetManager::class]
            ->registerStyle(
                $assetFactory->createInternalStyle(
                    'multilingualpress-site-flags-back',
                    'backend.min.css'
                )
            );

        $container[AssetManager::class]->enqueueStyle('multilingualpress-site-flags-back');

        $flagFilter = $container[FlagFilter::class];
        add_filter(
            TableList::FILTER_SITE_LANGUAGE_TAG,
            [$flagFilter, 'tableListPostsRelations'],
            10,
            2
        );
    }

    /**
     * @param Container $container
     */
    public function bootstrapFrontend(Container $container)
    {
        $assetFactory = $container['FlagsAssetFactory'];
        $container[AssetManager::class]
            ->registerStyle(
                $assetFactory->createInternalStyle(
                    'multilingualpress-site-flags-front',
                    'frontend.min.css'
                )
            );
        $container[AssetManager::class]->enqueueStyle('multilingualpress-site-flags-front');

        $flagFilter = $container[FlagFilter::class];
        add_filter('nav_menu_item_title', [$flagFilter, 'navMenuItems'], 10, 2);
        add_filter('multilingualpress.language_switcher_item_flag_url', [$flagFilter, 'languageSwitcherItems'], 10, 2);
    }

    /**
     * @param Container $container
     */
    public function bootstrapNetworkAdmin(Container $container)
    {
        $newSiteSettings = $container['FlagsNewSiteSettings'];

        add_action(
            SiteSettingsSectionView::ACTION_AFTER . '_mlp-new-site-settings',
            static function ($siteId) use ($newSiteSettings) {
                $newSiteSettings->renderView((int)$siteId);
            }
        );

        add_action(
            ParentSiteSettingsUpdater::ACTION_DEFINE_INITIAL_SETTINGS,
            [$container[SiteSettingsUpdater::class], 'defineInitialSettings']
        );
    }

    /**
     * @return string
     */
    protected function description(): string
    {
        return __(
            'Enable Site Flags for MultilingualPress.',
            'multilingualpress'
        );
    }

    /**
     * @return bool
     */
    protected function isSiteFlagsAddonActive(): bool
    {
        return is_plugin_active(self::OLD_FLAGS_ADDON_PATH);
    }
}
