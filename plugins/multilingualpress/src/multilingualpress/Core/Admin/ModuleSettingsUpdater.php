<?php

# -*- coding: utf-8 -*-

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Core\Admin;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

/**
 * Module settings updater.
 */
class ModuleSettingsUpdater
{
    const ACTION_SAVE_MODULES = 'multilingualpress.save_modules';
    const NAME_MODULE_SETTINGS = 'multilingualpress_modules';

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var array
     */
    private $modules = [];

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @param ModuleManager $moduleManager
     * @param Nonce $nonce
     */
    public function __construct(ModuleManager $moduleManager, Nonce $nonce)
    {
        $this->moduleManager = $moduleManager;
        $this->nonce = $nonce;
    }

    /**
     * Updates the plugin settings according to the data in the request.
     *
     * @param Request $request
     * @return bool
     */
    public function updateSettings(Request $request): bool
    {
        if (!$this->nonce->isValid()) {
            return false;
        }

        $this->modules = $request->bodyValue(
            self::NAME_MODULE_SETTINGS,
            INPUT_POST,
            FILTER_UNSAFE_RAW,
            FILTER_REQUIRE_ARRAY
        );

        $allModules = $this->moduleManager->modulesByState(ModuleManager::MODULE_STATE_ALL);
        $allModuleIds = array_keys($allModules);

        array_walk($allModuleIds, [$this, 'updateModule']);

        $this->moduleManager->persistModules();

        /**
         * Fires right after the module settings have been updated, and right before the redirect.
         *
         * @param Request $request
         */
        do_action(self::ACTION_SAVE_MODULES, $request);

        return true;
    }

    /**
     * Updates a single module according to the data in the request.
     *
     * @param string $moduleId
     */
    private function updateModule(string $moduleId)
    {
        empty($this->modules[$moduleId])
            ? $this->moduleManager->deactivateById($moduleId)
            : $this->moduleManager->activateById($moduleId);
    }
}
