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

namespace Inpsyde\MultilingualPress\Framework\Module;

use Inpsyde\MultilingualPress\Framework\Module\Exception\InvalidModule;
use Inpsyde\MultilingualPress\Framework\Module\Exception\ModuleAlreadyRegistered;

class ModuleManager
{
    const MODULE_STATE_ACTIVE = 1;
    const MODULE_STATE_ALL = 0;
    const MODULE_STATE_INACTIVE = 2;
    const OPTION = 'multilingualpress_modules';

    /**
     * @var Module[]
     */
    private $modules = [];

    /**
     * @var string
     */
    private $option;

    /**
     * @var bool[]
     */
    private $states;

    /**
     * @param string $option
     */
    public function __construct(string $option)
    {
        $this->option = $option;
        $this->states = (array)get_network_option(0, $this->option, []);
    }

    /**
     * Activates the module with the given ID.
     *
     * @param string $id
     * @return Module
     * @throws InvalidModule If there is no module with the given ID.
     */
    public function activateById(string $id): Module
    {
        if (!$this->isManagingModule($id)) {
            throw InvalidModule::forId($id, 'activate');
        }

        $this->states[$id] = true;

        return $this->moduleOfId($id)->activate();
    }

    /**
     * Deactivates the module with the given ID.
     *
     * @param string $id
     * @return Module
     * @throws InvalidModule If there is no module with the given ID.
     */
    public function deactivateById(string $id): Module
    {
        if (!$this->isManagingModule($id)) {
            throw InvalidModule::forId($id, 'deactivate');
        }

        $this->states[$id] = false;

        return $this->moduleOfId($id)->deactivate();
    }

    /**
     * Checks if any modules have been registered.
     *
     * @return bool
     */
    public function isManagingAnything(): bool
    {
        return !empty($this->modules);
    }

    /**
     * Checks if the module with the given ID has been registered.
     *
     * @param string $id
     * @return bool
     */
    public function isManagingModule(string $id): bool
    {
        return isset($this->modules[$id]);
    }

    /**
     * Checks if the module with the given ID is active.
     *
     * @param string $id
     * @return bool
     */
    public function isModuleActive(string $id): bool
    {
        return (bool)($this->states[$id] ?? false);
    }

    /**
     * Returns the module with the given ID.
     *
     * @param string $id
     * @return Module
     * @throws InvalidModule If there is no module with the given ID.
     */
    public function moduleOfId(string $id): Module
    {
        if (!$this->isManagingModule($id)) {
            throw InvalidModule::forId($id, 'read');
        }

        return $this->modules[$id];
    }

    /**
     * Returns all modules with the given state.
     *
     * @param int $state
     * @return Module[]
     */
    public function modulesByState(int $state = self::MODULE_STATE_ALL): array
    {
        if (!$this->modules) {
            return [];
        }

        if (self::MODULE_STATE_ACTIVE === $state) {
            return array_intersect_key(
                $this->modules,
                array_filter($this->states)
            );
        }

        if (self::MODULE_STATE_INACTIVE === $state) {
            return array_diff_key($this->modules, array_filter($this->states));
        }

        return $this->modules;
    }

    /**
     * Registers the given module.
     *
     * @param Module $module
     * @return bool
     * @throws ModuleAlreadyRegistered
     */
    public function register(Module $module): bool
    {
        $id = $module->id();

        if ($this->isManagingModule($id)) {
            throw ModuleAlreadyRegistered::forId($id, 'register');
        }

        $this->modules[$id] = $module;

        if (!$this->isManagingModule($id)) {
            $this->states[$id] = $module->isActive();
            $this->persistModules();
        }

        if ($this->isModuleActive($id)) {
            $module->activate();

            return true;
        }

        $module->deactivate();

        return false;
    }

    /**
     * Saves the modules persistently.
     *
     * @return bool
     */
    public function persistModules(): bool
    {
        return update_network_option(0, $this->option, $this->states);
    }

    /**
     * Unregisters the module with the given.
     *
     * @param string $moduleId
     * @return Module[]
     */
    public function unregisterById(string $moduleId): array
    {
        unset($this->modules[$moduleId], $this->states[$moduleId]);

        return $this->modules;
    }
}
