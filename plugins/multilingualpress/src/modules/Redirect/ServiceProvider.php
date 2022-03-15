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

namespace Inpsyde\MultilingualPress\Module\Redirect;

use Inpsyde\MultilingualPress\Asset\AssetFactory;
use Inpsyde\MultilingualPress\Core\Admin\NewSiteSettings;
use Inpsyde\MultilingualPress\Core\Admin\PluginSettingsUpdater;
use Inpsyde\MultilingualPress\Core\Admin\SiteSettings;
use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsUpdater;
use Inpsyde\MultilingualPress\Core\Frontend\AlternateLanguages;
use Inpsyde\MultilingualPress\Core\Locations;
use Inpsyde\MultilingualPress\Core\ServiceProvider as CoreServiceProvider;
use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTab;
use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTabData;
use Inpsyde\MultilingualPress\Framework\Admin\SitesListTableColumn;
use Inpsyde\MultilingualPress\Framework\Api\Translations;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Factory\NonceFactory;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Module\Exception\ModuleAlreadyRegistered;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\PluginProperties;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\Exception\NameOverwriteNotAllowed;
use Inpsyde\MultilingualPress\Framework\Service\Exception\WriteAccessOnLockedContainer;
use Inpsyde\MultilingualPress\Framework\Setting\SettingOption;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSetting;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionView;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingUpdater;
use Inpsyde\MultilingualPress\Framework\Setting\User\UserSetting;
use Inpsyde\MultilingualPress\Framework\Setting\User\UserSettingUpdater;
use Inpsyde\MultilingualPress\Module\Redirect\Settings\RedirectFallbackViewRenderer;
use Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository;
use Inpsyde\MultilingualPress\Module\Redirect\Settings\TabView;

use function Inpsyde\MultilingualPress\wpHookProxy;

final class ServiceProvider implements ModuleServiceProvider
{
    const MODULE_ID = 'redirect';

    const SETTING_NONCE_ACTION = 'multilingualpress_save_redirect_setting_nonce_';
    const MODULE_ASSETS_FACTORY_SERVICE_NAME = 'redirect_assets_factory';

    /**
     * @inheritdoc
     *
     * @throws NameOverwriteNotAllowed
     * @throws WriteAccessOnLockedContainer
     *
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    public function register(Container $container)
    {
        // phpcs:enable

        $container->addService(
            AcceptLanguageParser::class,
            static function (): AcceptLanguageParser {
                return new AcceptLanguageParser();
            }
        );

        $container->addService(
            LanguageNegotiator::class,
            static function (Container $container): LanguageNegotiator {
                return new LanguageNegotiator(
                    $container[Translations::class],
                    $container[ServerRequest::class],
                    $container[AcceptLanguageParser::class],
                    $container[Repository::class]
                );
            }
        );

        $container->addService(
            NoredirectPermalinkFilter::class,
            static function (): NoredirectPermalinkFilter {
                return new NoredirectPermalinkFilter();
            }
        );

        $container->addService(
            NoRedirectStorage::class,
            static function (): NoRedirectStorage {
                return is_user_logged_in() && wp_using_ext_object_cache()
                    ? new NoRedirectObjectCacheStorage()
                    : new NoRedirectSessionStorage();
            }
        );

        $container->addService(
            RedirectRequestChecker::class,
            static function (Container $container): RedirectRequestChecker {
                return new RedirectRequestChecker(
                    $container[Repository::class],
                    $container[NoRedirectStorage::class]
                );
            }
        );

        $container->share(
            self::MODULE_ASSETS_FACTORY_SERVICE_NAME,
            static function (Container $container): AssetFactory {
                $pluginProperties = $container[PluginProperties::class];

                $locations = new Locations();
                $locations
                    ->add(
                        'css',
                        $pluginProperties->dirPath() . 'src/modules/Redirect/public/css',
                        $pluginProperties->dirUrl() . 'src/modules/Redirect/public/css'
                    )
                    ->add(
                        'js',
                        $pluginProperties->dirPath() . 'src/modules/Redirect/public/js',
                        $pluginProperties->dirUrl() . 'src/modules/Redirect/public/js'
                    );

                return new AssetFactory($locations);
            }
        );

        $this->registerSettings($container);

        $this->registerRedirector($container);

        add_filter(
            CoreServiceProvider::ACTION_BUILD_TABS,
            static function (array $tabs) use ($container): array {

                $settingsRepository = $container[Repository::class];
                $nonceFactory = $container[NonceFactory::class];
                $moduleManager = $container[ModuleManager::class];

                if ($moduleManager->isModuleActive(self::MODULE_ID)) {
                    $tabs['redirect'] = new SettingsPageTab(
                        new SettingsPageTabData(
                            'redirect',
                            esc_html__('Redirect', 'multilingualpress'),
                            'redirect'
                        ),
                        new TabView(
                            $nonceFactory->create(['save_module_redirect_settings']),
                            new RedirectFallbackViewRenderer($settingsRepository)
                        )
                    );
                }

                return $tabs;
            }
        );
    }

    /**
     * @param Container $container
     * @throws NameOverwriteNotAllowed
     * @throws WriteAccessOnLockedContainer
     */
    private function registerRedirector(Container $container)
    {
        $container->addService(
            LanguageUrlDictionaryFactory::class,
            static function (Container $container): LanguageUrlDictionaryFactory {
                return new LanguageUrlDictionaryFactory(
                    new TranslationSearchArgs(),
                    $container[LanguageNegotiator::class]
                );
            }
        );

        $container->addService(
            Redirector::class,
            static function (Container $container): Redirector {

                $negotiator = $container[LanguageNegotiator::class];

                /**
                 * Filters the redirector type.
                 *
                 * @param string $type
                 */
                $type = apply_filters(
                    Redirector::FILTER_REDIRECTOR_TYPE,
                    Redirector::TYPE_PHP
                );

                if ($type === Redirector::TYPE_JAVASCRIPT) {
                    return new JsRedirector(
                        $container[LanguageUrlDictionaryFactory::class],
                        $container[AssetManager::class],
                        $container[Repository::class]
                    );
                }

                return new PhpRedirector(
                    $negotiator,
                    $container[NoRedirectStorage::class],
                    $container[ServerRequest::class],
                    $container[AcceptLanguageParser::class]
                );
            }
        );

        $container->addService(
            NotFoundSiteRedirect::class,
            static function (Container $container): NotFoundSiteRedirect {
                return new NotFoundSiteRedirect(
                    $container[Repository::class],
                    $container[NoRedirectStorage::class]
                );
            }
        );
    }

    /**
     * @param Container $container
     *
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     * @throws NameOverwriteNotAllowed
     * @throws WriteAccessOnLockedContainer
     */
    private function registerSettings(Container $container)
    {
        // phpcs:enable

        $container->share(
            Repository::class,
            static function (): Repository {
                return new Repository();
            }
        );

        $container->share(
            'redirect_site_settings',
            static function (Container $container): array {
                $repository = $container[Repository::class];
                return [
                    new SettingOption(
                        $repository::OPTION_SITE_ENABLE_REDIRECT,
                        $repository::OPTION_SITE_ENABLE_REDIRECT,
                        'Enable automatic redirect'
                    ),
                    new SettingOption(
                        $repository::OPTION_SITE_ENABLE_REDIRECT_FALLBACK,
                        $repository::OPTION_SITE_ENABLE_REDIRECT_FALLBACK,
                        'Redirect Fallback site for this language'
                    ),
                ];
            }
        );

        $container->addService(
            RedirectSiteSettings::class,
            static function (Container $container): RedirectSiteSettings {
                return new RedirectSiteSettings(
                    $container->get('redirect_site_settings'),
                    $container[NonceFactory::class]->create([self::SETTING_NONCE_ACTION . 'site']),
                    $container[Repository::class]
                );
            }
        );

        $container->addService(
            RedirectUserSetting::class,
            static function (Container $container): RedirectUserSetting {
                return new RedirectUserSetting(
                    Repository::META_KEY_USER,
                    $container[NonceFactory::class]->create([self::SETTING_NONCE_ACTION . 'user']),
                    $container[Repository::class]
                );
            }
        );

        $container->addService(
            Settings\Updater::class,
            static function (Container $container): Settings\Updater {
                return new Settings\Updater(
                    $container[NonceFactory::class]->create(['save_module_redirect_settings']),
                    $container[Repository::class]
                );
            }
        );
    }

    /**
     * @inheritdoc
     * @param ModuleManager $moduleManager
     * @return bool
     * @throws ModuleAlreadyRegistered
     */
    public function registerModule(ModuleManager $moduleManager): bool
    {
        return $moduleManager->register(
            new Module(
                self::MODULE_ID,
                [
                    'description' => __(
                        'Enable the Redirect checkbox on each site: this allows you to enable/disable the automatic redirection feature according to the user browser language settings.',
                        'multilingualpress'
                    ),
                    'name' => __('Redirect', 'multilingualpress'),
                    'active' => true,
                ]
            )
        );
    }

    /**
     * @inheritdoc
     * @param Container $container
     * @throws \Throwable
     */
    public function activateModule(Container $container)
    {
        $userSetting = new UserSetting(
            $container[RedirectUserSetting::class],
            new UserSettingUpdater(
                Repository::META_KEY_USER,
                $container[ServerRequest::class],
                $container[NonceFactory::class]->create([self::SETTING_NONCE_ACTION . 'user'])
            )
        );
        $userSetting->register();

        if (is_admin()) {
            $this->activateModuleForAdmin($container);

            return;
        }

        $this->activateModuleForFrontend($container);
    }

    /**
     * Performs various admin-specific tasks on module activation.
     *
     * @param Container $container
     */
    private function activateModuleForAdmin(Container $container)
    {
        $setting = new SiteSetting(
            $container[RedirectSiteSettings::class],
            new SiteSettingUpdater(
                Repository::OPTION_SITE,
                $container[ServerRequest::class],
                $container[NonceFactory::class]->create([self::SETTING_NONCE_ACTION . 'site'])
            )
        );

        $setting->register(
            SiteSettingsSectionView::ACTION_AFTER . '_' . SiteSettings::ID,
            SiteSettingsUpdater::ACTION_UPDATE_SETTINGS
        );

        add_action(
            PluginSettingsUpdater::ACTION_UPDATE_PLUGIN_SETTINGS,
            [$container[Settings\Updater::class], 'updateSettings']
        );

        if (is_network_admin()) {
            $this->activateModuleForNetworkAdmin($container, $setting);
        }
    }

    /**
     * Performs various admin-specific tasks on module activation.
     *
     * @param Container $container
     * @param SiteSetting $setting
     */
    private function activateModuleForNetworkAdmin(Container $container, SiteSetting $setting)
    {
        $setting->register(
            SiteSettingsSectionView::ACTION_AFTER . '_' . NewSiteSettings::SECTION_ID,
            SiteSettingsUpdater::ACTION_DEFINE_INITIAL_SETTINGS
        );

        if ('sites.php' !== ($GLOBALS['pagenow'] ?? '')) {
            return;
        }

        $settingsRepository = $container[Repository::class];
        $sitesListTableColumn = new SitesListTableColumn(
            'multilingualpress.redirect',
            __('Redirect', 'multilingualpress'),
            static function (string $column, int $siteId) use ($settingsRepository): string {
                return $settingsRepository->isRedirectSettingEnabledForSite($siteId)
                    ? '<span class="dashicons dashicons-yes"></span>'
                    : '';
            }
        );

        $sitesListTableColumn->register();
    }

    /**
     * Performs various admin-specific tasks on module activation.
     *
     * @param Container $container
     * @throws \Throwable
     */
    private function activateModuleForFrontend(Container $container)
    {
        $assetFactory = $container[self::MODULE_ASSETS_FACTORY_SERVICE_NAME];

        $container[AssetManager::class]
            ->registerScript(
                $assetFactory->createInternalScript(
                    JsRedirector::SCRIPT_HANDLE,
                    'frontend.min.js',
                    ['jquery']
                )
            );

        $filter = $container[NoredirectPermalinkFilter::class];
        $filter->enable();

        add_filter(
            AlternateLanguages::FILTER_HREFLANG_URL,
            wpHookProxy([$filter, 'removeNoRedirectQueryArgument'])
        );

        if ($container[RedirectRequestChecker::class]->isRedirectRequest()) {
            add_action(
                'template_redirect',
                [$container[Redirector::class], 'redirect'],
                1
            );
            add_action(
                Redirector::ACTION_TARGET_NOT_FOUND,
                [$container[NotFoundSiteRedirect::class], 'redirect']
            );
        }
    }
}
