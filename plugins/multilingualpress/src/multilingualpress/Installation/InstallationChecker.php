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
use Inpsyde\MultilingualPress\MultilingualPress;

/**
 * MultilingualPress installation checker.
 */
class InstallationChecker
{
    /**
     * @var SystemChecker
     */
    private $checker;

    /**
     * @var PluginProperties
     */
    private $properties;

    /**
     * @param SystemChecker $checker
     * @param PluginProperties $properties
     */
    public function __construct(SystemChecker $checker, PluginProperties $properties)
    {
        $this->checker = $checker;
        $this->properties = $properties;
    }

    /**
     * Checks the installation for compliance with the system requirements and return one of the
     * SystemChecker status flags.
     *
     * @return int
     */
    public function check(): int
    {
        $installationCheck = $this->checker->checkInstallation();

        if (
            SystemChecker::PLUGIN_DEACTIVATED === $installationCheck
            || SystemChecker::INSTALLATION_OK !== $installationCheck
        ) {
            return $installationCheck;
        }

        list($installedVersion, $currentVersion) = $this->versions();

        $versionStatus = $this->checker->checkVersion($installedVersion, $currentVersion);

        /**
         * Fires right after the MultilingualPress version check.
         *
         * @param int $versionStatus
         * @param SemanticVersionNumber $installedVersion
         */
        do_action(
            SystemChecker::ACTION_CHECKED_VERSION,
            $versionStatus,
            $installedVersion
        );

        update_network_option(
            0,
            MultilingualPress::OPTION_VERSION,
            (string)$currentVersion
        );

        return $installationCheck;
    }

    /**
     * Returns an array with the installed and the current version of MultilingualPress.
     *
     * @return SemanticVersionNumber[]
     */
    private function versions(): array
    {
        $installed = get_network_option(0, MultilingualPress::OPTION_VERSION);

        return [
            new SemanticVersionNumber((string)$installed),
            new SemanticVersionNumber($this->properties->version()),
        ];
    }
}
