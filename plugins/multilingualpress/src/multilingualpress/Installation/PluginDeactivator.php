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

use Inpsyde\MultilingualPress\Framework\Admin\AdminNotice;

/**
 * Deactivates specific plugin.
 */
class PluginDeactivator
{
    /**
     * @var string[]
     */
    private $errors;

    /**
     * @var string
     */
    private $pluginBaseName;

    /**
     * @var string
     */
    private $pluginName;

    /**
     * @param string $pluginBaseName
     * @param string $pluginName
     * @param string[] $errors
     */
    public function __construct(string $pluginBaseName, string $pluginName, array $errors = [])
    {
        $this->pluginBaseName = $pluginBaseName;
        $this->pluginName = $pluginName;
        $this->errors = $errors;
    }

    /**
     * Deactivates the plugin, and renders an according admin notice.
     */
    public function deactivatePlugin()
    {
        deactivate_plugins($this->pluginBaseName);

        // Suppress the "Plugin activated" notice.
        unset($_GET['activate']); // phpcs:ignore

        $this->renderAdminNotice();
    }

    /**
     * Renders an admin notice informing about the plugin deactivation,
     * including potential error messages.
     */
    private function renderAdminNotice()
    {
        // translators: %s: plugin name.
        $message = esc_html__(
            'The plugin %s has been deactivated.',
            'multilingualpress'
        );

        $title = sprintf($message, $this->pluginName);

        if ($this->errors) {
            AdminNotice::error(...$this->errors)->withTitle($title)->renderNow();

            return;
        }

        AdminNotice::info($title)->renderNow();
    }
}
