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

namespace Inpsyde\MultilingualPress\Core;

use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Module\WooCommerce;

/**
 * MultilingualPress Modules Deactivator
 */
class ModuleDeactivator
{
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * ModuleDeactivator constructor
     *
     * @param ModuleManager $moduleManager
     */
    public function __construct(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * Deactivate WooCommerce Module
     */
    public function deactivateWooCommerce()
    {
        $this->moduleManager->deactivateById(WooCommerce\ServiceProvider::MODULE_ID);
        $this->moduleManager->persistModules();
    }
}
