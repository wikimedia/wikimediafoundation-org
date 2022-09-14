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

namespace Inpsyde\MultilingualPress\Module\AltLanguageTitleInAdminBar;

use Inpsyde\MultilingualPress\Core\Admin\AltLanguageTitleSiteSetting;
use Inpsyde\MultilingualPress\Core\Admin\NewSiteSettings;
use Inpsyde\MultilingualPress\Core\Admin\SiteSettings;
use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsUpdater;
use Inpsyde\MultilingualPress\Framework\Factory\NonceFactory;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Module\ModuleServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSetting;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionView;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingUpdater;

use function Inpsyde\MultilingualPress\wpHookProxy;

/**
 * Module service provider.
 */
final class ServiceProvider implements ModuleServiceProvider
{
    const MODULE_ID = 'alternative_language_title';
    const SETTING_NONCE_ACTION = 'multilingualpress_save_alt_language_title_setting_nonce_';

    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container->addService(
            AdminBarCustomizer::class,
            static function (Container $container): AdminBarCustomizer {
                return new AdminBarCustomizer($container[SettingsRepository::class]);
            }
        );

        $this->registerSettings($container);
    }

    /**
     * @inheritdoc
     */
    public function registerModule(ModuleManager $moduleManager): bool
    {
        return $moduleManager->register(
            new Module(
                self::MODULE_ID,
                [
                    'description' => __(
                        'Enable the Alternative Language Title field on each site: this allows you to create an alternative site title to show on the site admin bar.',
                        'multilingualpress'
                    ),
                    'name' => __('Alternative Language Title', 'multilingualpress'),
                    'active' => false,
                ]
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function activateModule(Container $container)
    {
        $this->activateModuleForAdmin($container);

        $customizer = $container[AdminBarCustomizer::class];

        add_filter('admin_bar_menu', wpHookProxy([$customizer, 'replaceSiteNodes']), 11);

        if (!is_network_admin()) {
            add_filter('admin_bar_menu', wpHookProxy([$customizer, 'replaceSiteName']), 31);
        }
    }

    /**
     * @param Container $container
     */
    private function registerSettings(Container $container)
    {
        $container->share(
            SettingsRepository::class,
            static function (): SettingsRepository {
                return new SettingsRepository();
            }
        );

        $container->addService(
            AltLanguageTitleSiteSetting::class,
            static function (Container $container): AltLanguageTitleSiteSetting {
                return new AltLanguageTitleSiteSetting(
                    SettingsRepository::OPTION_SITE,
                    $container[NonceFactory::class]->create([self::SETTING_NONCE_ACTION . 'site']),
                    $container[SettingsRepository::class]
                );
            }
        );
    }

    /**
     * @param Container $container
     */
    private function activateModuleForAdmin(Container $container)
    {
        $setting = new SiteSetting(
            $container[AltLanguageTitleSiteSetting::class],
            new SiteSettingUpdater(
                SettingsRepository::OPTION_SITE,
                $container[ServerRequest::class],
                $container[NonceFactory::class]->create([self::SETTING_NONCE_ACTION . 'site'])
            )
        );

        $setting->register(
            SiteSettingsSectionView::ACTION_AFTER . '_' . SiteSettings::ID,
            SiteSettingsUpdater::ACTION_UPDATE_SETTINGS
        );

        if (is_network_admin()) {
            $this->activateModuleForNetworkAdmin($setting);
        }
    }

    /**
     * @param SiteSetting $setting
     */
    private function activateModuleForNetworkAdmin(SiteSetting $setting)
    {
        $setting->register(
            SiteSettingsSectionView::ACTION_AFTER . '_' . NewSiteSettings::SECTION_ID,
            SiteSettingsUpdater::ACTION_DEFINE_INITIAL_SETTINGS
        );
    }
}
