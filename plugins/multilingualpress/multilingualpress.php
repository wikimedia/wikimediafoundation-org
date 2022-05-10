<?php // phpcs:ignore
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @wordpress-plugin
 * Plugin Name: MultilingualPress
 * Plugin URI: https://multilingualpress.org/
 * Description: The multisite-based plugin for your multilingual WordPress websites.
 * Author: Inpsyde GmbH
 * Author URI: https://inpsyde.com
 * Version: 3.9.1
 * Text Domain: multilingualpress
 * Domain Path: /languages/
 * License: GPLv2+
 * Network: true
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * 
 */

namespace Inpsyde\MultilingualPress;

// phpcs:disable PSR1.Files.SideEffects
// phpcs:disable NeutronStandard.StrictTypes.RequireStrictTypes
// phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
// phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong

use Exception;

defined('ABSPATH') or die();

/**
 * @param $function
 *
 * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
 */
function deactivateNotice($function)
{
    if (!is_callable($function)) {
        return;
    }
    $hooks = [
        'admin_notices',
        'network_admin_notices',
    ];
    foreach ($hooks as $hook) {
        add_action($hook, static function () use ($function) {
            $function();

            deactivate_plugins(plugin_basename(__FILE__));

            // Suppress the "Plugin activated" notice.
            unset($_GET['activate']); // phpcs:ignore
        });
    }
}

/** @noinspection ConstantCanBeUsedInspection */
if (version_compare(PHP_VERSION, '7.2', '<')) {
    deactivateNotice(static function () {
        $message = __(
            'MultilingualPress 3 requires at least  PHP version 7.2. <br />Please ask your server administrator to update your environment to PHP version 7.2.',
            'multilingualpress'
        );

        printf(
            '<div class="notice notice-error"><span class="notice-title">%1$s</span><p>%2$s</p></div>',
            esc_html__(
                'The plugin MultilingualPress has been deactivated',
                'multilingualpress'
            ),
            wp_kses($message, ['br' => true])
        );
    });
    return;
}

if (!function_exists('is_plugin_active')) {
    /** @noinspection PhpIncludeInspection */
    require_once untrailingslashit(ABSPATH) . '/wp-admin/includes/plugin.php';
}
if (is_plugin_active('multilingual-press/multilingual-press.php')) {
    deactivateNotice(static function () {
        $message = __(
            'You try to activate MLP Version 3 but you already have MLP Version 2 activated. Please check out our <a href="https://multilingualpress.org/docs/getting-started-with-multilingualpress-3/">guide</a> before you continue!',
            'multilingualpress'
        );

        printf(
            '<div class="notice notice-error"><span class="notice-title">%1$s</span><p>%2$s</p></div>',
            esc_html__(
                'The plugin MultilingualPress has been deactivated',
                'multilingualpress'
            ),
            wp_kses($message, ['a' => ['href' => true]])
        );
    });
    return;
}

/**
 * Loads definitions and/or autoloader.
 *
 * @param string $rootDir
 * @throws Exception
 */
function autoload(string $rootDir)
{
    $autoloadPath = "$rootDir/vendor/autoload.php";
    if (!file_exists($autoloadPath)) {
        throw new Exception("The autoload file({$autoloadPath}) doesn't exist");
    }

    /* @noinspection PhpIncludeInspection */
    require_once "$rootDir/src/inc/functions.php";

    /* @noinspection PhpIncludeInspection */
    require_once $autoloadPath;
}

/**
 * Bootstraps MultilingualPress.
 *
 * @return bool
 *
 * @wp-hook plugins_loaded
 * @throws \Throwable
 * @return bool
 */
function bootstrap()
{
    $bootstrapped = false;

    try {
        /** @var Framework\Service\Container $container */
        $container = resolve(null);
        $container = $container->shareValue(
            Framework\PluginProperties::class,
            new Framework\PluginProperties(__FILE__)
        );

        $providers = new Framework\Service\ServiceProvidersCollection();

        $modules = [
            new Activation\ServiceProvider(),
            new Api\ServiceProvider(),
            new Asset\ServiceProvider(),
            new Cache\ServiceProvider(),
            new Core\ServiceProvider(),
            new Auth\ServiceProvider(),
            new Database\ServiceProvider(),
            new Factory\ServiceProvider(),
            new Installation\ServiceProvider(),
            new Integration\ServiceProvider(),
            new NavMenu\ServiceProvider(),
            new SiteDuplication\ServiceProvider(),
            new TranslationUi\ServiceProvider(),
            new Translator\ServiceProvider(),
            new Module\AltLanguageTitleInAdminBar\ServiceProvider(),
            new Module\Redirect\ServiceProvider(),
            new Module\Trasher\ServiceProvider(),
            new Module\LanguageManager\ServiceProvider(),
            new Module\LanguageSwitcher\ServiceProvider(),
            new Module\WooCommerce\ServiceProvider(),
            new Module\QuickLinks\ServiceProvider(),
            new Onboarding\ServiceProvider(),
            new Schedule\ServiceProvider(),
            new Customizer\ServiceProvider(),
            new Module\ACF\ServiceProvider(),
            new License\ServiceProvider(),
            new Module\BeaverBuilder\ServiceProvider(),
            new Module\Elementor\ServiceProvider(),
            new Module\User\ServiceProvider(),
        ];

        $modules = apply_filters('multilingualpress.modules', $modules);

        foreach ($modules as $module) {
            $providers->add($module);
        }

        $multilingualpress = new MultilingualPress($container, $providers);

        /**
         * Fires right before MultilingualPress gets bootstrapped.
         *
         * Hook here to add custom service providers via
         * `ServiceProviderCollection::add_service_provider()`.
         *
         * @param Framework\Service\ServiceProvidersCollection $providers
         */
        do_action(ACTION_ADD_SERVICE_PROVIDERS, $providers);

        $bootstrapped = $multilingualpress->bootstrap();

        unset($providers);
    } catch (\Throwable $thr) {
        do_action(ACTION_LOG, 'error', $thr->getMessage(), compact('thr'));

        if (defined('WP_DEBUG') && WP_DEBUG) {
            throw $thr;
        }
    }

    return $bootstrapped;
}

/**
 * Triggers a plugin-specific activation action third parties can listen to.
 *
 * @wp-hook activate_{$plugin}
 */
function activate()
{
    /**
     * Fires when MultilingualPress is about to be activated.
     */
    do_action(ACTION_ACTIVATION);

    add_action(
        'activated_plugin',
        static function (string $plugin) {
            if (plugin_basename(__FILE__) === $plugin) {
                // Bootstrap MultilingualPress to take care of installation or upgrade routines.
                bootstrap();
            }
        },
        0
    );
}

/**
 * Load missed WordPress functions.
 */
function loadWordPressFunctions()
{
    if (!function_exists('wp_get_available_translations')) {
        require_once(ABSPATH . 'wp-admin/includes/translation-install.php');
    }
}

(static function (string $rootFile) {
    $rootDir = dirname($rootFile);

    try {
        autoload($rootDir);

        $modularity = "{$rootDir}/src/inc/modularity.php";
        if (file_exists($modularity)) {
            $moduleActivator = require_once $modularity;
            $moduleActivator($rootDir);
        }

        add_action('plugins_loaded', __NAMESPACE__ . '\\bootstrap', 0);

        register_activation_hook($rootFile, __NAMESPACE__ . '\\activate');

        loadWordPressFunctions();
    } catch (Exception $exception) {
        deactivateNotice(static function () use ($exception) {
            printf(
                '<div class="notice notice-error"><span class="notice-title">%1$s</span><p>%2$s</p></div>',
                esc_html__(
                    'The plugin MultilingualPress has been deactivated',
                    'multilingualpress'
                ),
                wp_kses($exception->getMessage(), [])
            );
        });
        return;
    }
})(__FILE__);
