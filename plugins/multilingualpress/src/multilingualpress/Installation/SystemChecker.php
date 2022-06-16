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

use Inpsyde\MultilingualPress\Framework\PluginProperties;
use Inpsyde\MultilingualPress\Framework\SemanticVersionNumber;
use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository;

/**
 * Performs various system-specific checks.
 */
class SystemChecker
{
    const FILTER_FORCE_CHECK = 'multilingualpress.force_system_check';
    const ACTION_CHECKED_VERSION = 'multilingualpress.checked_version';
    const WRONG_PAGE_FOR_CHECK = 1;
    const INSTALLATION_OK = 2;
    const PLUGIN_DEACTIVATED = 3;
    const VERSION_OK = 4;
    const NEEDS_INSTALLATION = 5;
    const NEEDS_UPGRADE = 6;
    const LEGACY_DETECTED = 7;
    const MINIMUM_PHP_VERSION = '7.0.0';
    const MINIMUM_WORDPRESS_VERSION = '4.8.3';

    /**
     * @var string[]
     */
    private $errors = [];

    /**
     * @var PluginProperties
     */
    private $pluginProperties;

    /**
     * @var SiteRelationsChecker
     */
    private $siteRelationsChecker;

    /**
     * @var SiteSettingsRepository
     */
    private $siteSettingsRepository;

    /**
     * @param PluginProperties $pluginProperties
     * @param SiteRelationsChecker $siteRelationsChecker
     * @param SiteSettingsRepository $siteSettingsRepository
     */
    public function __construct(
        PluginProperties $pluginProperties,
        SiteRelationsChecker $siteRelationsChecker,
        SiteSettingsRepository $siteSettingsRepository
    ) {

        $this->pluginProperties = $pluginProperties;
        $this->siteRelationsChecker = $siteRelationsChecker;
        $this->siteSettingsRepository = $siteSettingsRepository;
    }

    /**
     * Checks the installation for compliance with the system requirements.
     *
     * @return int
     */
    public function checkInstallation(): int
    {
        /**
         * Filters if the system check should be forced regardless of the context.
         *
         * @param bool $force
         */
        $forceCheck = (bool)apply_filters(self::FILTER_FORCE_CHECK, false);

        if (!$forceCheck && !$this->isContextValid()) {
            return static::WRONG_PAGE_FOR_CHECK;
        }

        $this->checkLegacyVersion();
        $this->checkWordpressVersion();
        $this->checkMultisite();
        $this->checkPluginActivation();

        if (!$this->errors) {
            $this->siteRelationsChecker->checkRelations();

            return static::INSTALLATION_OK;
        }

        $deactivator = new PluginDeactivator(
            $this->pluginProperties->basename(),
            $this->pluginProperties->name(),
            $this->errors
        );

        add_action('admin_notices', [$deactivator, 'deactivatePlugin'], 0);
        add_action('network_admin_notices', [$deactivator, 'deactivatePlugin'], 0);

        return static::PLUGIN_DEACTIVATED;
    }

    /**
     * Checks the installed plugin version.
     *
     * @param SemanticVersionNumber $installedMlpVersion
     * @param SemanticVersionNumber $currentMlpVersion
     * @return int
     */
    public function checkVersion(
        SemanticVersionNumber $installedMlpVersion,
        SemanticVersionNumber $currentMlpVersion
    ): int {

        $isUpToDate = version_compare(
            (string)$installedMlpVersion,
            (string)$currentMlpVersion,
            '>='
        );

        if ($isUpToDate) {
            return static::VERSION_OK;
        }

        if ($this->siteSettingsRepository->allSettings()) {
            return static::NEEDS_UPGRADE;
        }

        return static::NEEDS_INSTALLATION;
    }

    /**
     * Checks if an old version of MLP is installed in the system.
     * @return void
     */
    public function checkLegacyVersion()
    {
        $isCheckLegacy = apply_filters('multilingualpress.is_check_legacy', true);
        if (!$isCheckLegacy) {
            return;
        }

        if (get_site_option('inpsyde_multilingual')) {
            $message = __(
                'It seems that an old version of MultilingualPress is installed in this website.',
                'multilingualpress'
            );
            $message .= ' ';
            /* translators: 1: version number, 2: plugin name */
            $message .= __(
                'Version %1$s of %2$s is not compatible with that old version.',
                'multilingualpress'
            );

            $this->errors[] = sprintf(
                $message,
                $this->pluginProperties->version(),
                $this->pluginProperties->name()
            );
        }
    }

    /**
     * Checks if the current WordPress version is the required version higher, and collects
     * potential error messages.
     */
    private function checkWordpressVersion()
    {
        $current = new SemanticVersionNumber($GLOBALS['wp_version'] ?? '');
        $required = new SemanticVersionNumber(self::MINIMUM_WORDPRESS_VERSION);

        if (version_compare((string)$current, (string)$required, '>=')) {
            return;
        }

        /* translators: 1: required WordPress version, 2: current WordPress version */
        $message = __(
            'This plugin requires WordPress version %1$s, your version %2$s is too old. Please upgrade.',
            'multilingualpress'
        );

        $this->errors[] = sprintf($message, (string)$required, (string)$current);
    }

    /**
     * Checks if this is a multisite installation, and collects potential error messages.
     */
    private function checkMultisite()
    {
        if (is_multisite()) {
            return;
        }

        $message = esc_html__(
            'This plugin needs to run in a multisite. ',
            'multilingualpress'
        );

        $message .= sprintf(
            '<a href="%s">%s</a>',
            'https://multilingualpress.org/docs/how-to-install-wordpress-multisite/',
            esc_html__(
                'Please convert this WordPress installation to multisite.',
                'multilingualpress'
            )
        );

        $this->errors[] = $message;
    }

    /**
     * Checks if MultilingualPress has been activated network-wide, and collects
     * potential error messages.
     */
    private function checkPluginActivation()
    {
        if (!is_multisite()) {
            return;
        }

        $pluginPath = realpath($this->pluginProperties->filePath());

        foreach (wp_get_active_network_plugins() as $plugin) {
            if (realpath($plugin) === $pluginPath) {
                return;
            }
        }

        $message = esc_html__(
            'This plugin must be activated for the network.',
            'multilingualpress'
        );

        $message .= '<br>' .
            sprintf(
                '<a href="%s">%s</a>',
                esc_url(network_admin_url('plugins.php')),
                esc_html__(
                    'Please use the network plugin administration.',
                    'multilingualpress'
                )
            );

        $this->errors[] = $message;
    }

    /**
     * Checks if the context is valid.
     *
     * @return bool
     */
    private function isContextValid(): bool
    {
        if (wp_doing_ajax() || !is_admin()) {
            return false;
        }

        return 'plugins.php' === $GLOBALS['pagenow'];
    }
}
