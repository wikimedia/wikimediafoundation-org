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

namespace Inpsyde\MultilingualPress\Core\Admin;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

use function Inpsyde\MultilingualPress\redirectAfterSettingsUpdate;

/**
 * Plugin settings updater.
 */
class PluginSettingsUpdater
{
    const ACTION = 'update_multilingualpress_settings';
    const ACTION_UPDATE_PLUGIN_SETTINGS = 'multilingualpress.update_plugin_settings';

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Nonce $nonce
     * @param Request $request
     */
    public function __construct(Nonce $nonce, Request $request)
    {
        $this->nonce = $nonce;
        $this->request = $request;
    }

    /**
     * Updates the plugin settings according to the data in the request.
     */
    public function updateSettings()
    {
        if (!$this->nonce->isValid()) {
            wp_die('Invalid', 'Invalid', 403);
        }

        /**
         * Fires when the plugin settings are about to get updated.
         *
         * @param Request $request
         */
        do_action(self::ACTION_UPDATE_PLUGIN_SETTINGS, $this->request);

        redirectAfterSettingsUpdate();
    }
}
