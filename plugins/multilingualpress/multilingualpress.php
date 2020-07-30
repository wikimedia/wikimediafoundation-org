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
 * Version: 3.4.0
 * Text Domain: multilingualpress
 * Domain Path: /languages/
 * License: GPLv2+
 * Network: true
 * Requires at least: 4.8
 * Requires PHP: 7.0
 * 
 */

namespace Inpsyde\MultilingualPress;

// phpcs:disable PSR1.Files.SideEffects
// phpcs:disable NeutronStandard.StrictTypes.RequireStrictTypes
// phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration

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
        add_action($hook, function () use ($function) {
            $function();

            deactivate_plugins(plugin_basename(__FILE__));

            // Suppress the "Plugin activated" notice.
            unset($_GET['activate']); // phpcs:ignore
        });
    }
}

/** @noinspection ConstantCanBeUsedInspection */
if (version_compare(PHP_VERSION, '7', '<')) {
    deactivateNotice(function () {
        $message = __(
            'MultilingualPress 3 requires at least PHP version 7. <br />Please ask your server administrator to update your environment to PHP version 7.',
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
    deactivateNotice(function () {
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

// phpcs:ignore
function autoload()
{
    require_once __DIR__ . '/src/inc/functions.php';

    static $done;
    if (is_bool($done)) {
        return $done;
    }
    if (class_exists(MultilingualPress::class)) {
        $done = true;

        return true;
    }
    if (is_readable(__DIR__ . '/autoload.php')) {
        /** @noinspection PhpIncludeInspection */
        require_once __DIR__ . '/autoload.php';
        $done = true;

        return true;
    }
    if (is_readable(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
        $done = true;

        return true;
    }
    $done = false;

    return false;
}

if (!autoload()) {
    return;
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
        $providers
            ->add(new Activation\ServiceProvider())
            ->add(new Api\ServiceProvider())
            ->add(new Asset\ServiceProvider())
            ->add(new Cache\ServiceProvider())
            ->add(new Core\ServiceProvider())
            ->add(new Auth\ServiceProvider())
            ->add(new Database\ServiceProvider())
            ->add(new Factory\ServiceProvider())
            ->add(new Installation\ServiceProvider())
            ->add(new Integration\ServiceProvider())
            ->add(new NavMenu\ServiceProvider())
            ->add(new SiteDuplication\ServiceProvider())
            ->add(new TranslationUi\ServiceProvider())
            ->add(new Translator\ServiceProvider())
            ->add(new Module\AltLanguageTitleInAdminBar\ServiceProvider())
            ->add(new Module\Redirect\ServiceProvider())
            ->add(new Module\Trasher\ServiceProvider())
            ->add(new Module\LanguageManager\ServiceProvider())
            ->add(new Module\LanguageSwitcher\ServiceProvider())
            ->add(new Module\WooCommerce\ServiceProvider())
            ->add(new Module\QuickLinks\ServiceProvider())
            ->add(new Onboarding\ServiceProvider())
            ->add(new Schedule\ServiceProvider());

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
        function (string $plugin) {
            if (plugin_basename(__FILE__) === $plugin) {
                // Bootstrap MultilingualPress to take care of installation or upgrade routines.
                bootstrap();
            }
        },
        0
    );
}

(function (string $rootFile) {
    $rootDir = dirname($rootFile);
    $modularity = "{$rootDir}/src/inc/modularity.php";
    if (file_exists($modularity)) {
        $moduleActivator = require_once $modularity;
        $moduleActivator($rootDir);
    }

    add_action('plugins_loaded', __NAMESPACE__ . '\\bootstrap', 0);

    register_activation_hook($rootFile, __NAMESPACE__ . '\\activate');
})(__FILE__);
