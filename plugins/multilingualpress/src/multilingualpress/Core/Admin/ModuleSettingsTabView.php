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

use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView;
use Inpsyde\MultilingualPress\Framework\Module\Module;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

use function Inpsyde\MultilingualPress\printNonceField;

/**
 * Module settings tab view.
 */
final class ModuleSettingsTabView implements SettingsPageView
{
    const ACTION_IN_MODULE_LIST = 'multilingualpress.in_module_list';
    const FILTER_SHOW_MODULE = 'multilingualpress.show_module';

    /**
     * @var ModuleManager
     */
    private $moduleManager;

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
     * @inheritdoc
     */
    public function render()
    {
        ?>
        <table class="widefat mlp-settings-table mlp-module-settings">
            <?php
            foreach ($this->moduleManager->modulesByState() as $id => $module) {
                /**
                 * Filters if the module should be listed on the settings page.
                 *
                 * @param bool $showModule
                 */
                if (apply_filters(self::FILTER_SHOW_MODULE . "_{$id}", true)) {
                    $this->renderModule($module);
                }
            }

            /**
             * Fires at the end of but still inside the module list on the settings page.
             */
            do_action(self::ACTION_IN_MODULE_LIST);
            ?>
        </table>
        <?php
        /**
         * Fires right after after the module list on the settings page.
         */
        do_action('multilingualpress.after_module_list');

        printNonceField($this->nonce);
    }

    /**
     * Renders the markup for the given module.
     *
     * @param Module $module
     */
    private function renderModule(Module $module)
    {
        $isActive = $module->isActive();
        $isDisabled = $module->isDisabled();
        $name = ModuleSettingsUpdater::NAME_MODULE_SETTINGS . '[' . $module->id() . ']';

        $id = 'multilingualpress-module-' . $module->id();
        ?>
        <tr class="<?= esc_attr($isActive ? 'active' : 'inactive') ?>">
            <th class="check-column" scope="row">
                <input
                    type="checkbox"
                    name="<?= esc_attr($name) ?>"
                    value="1"
                    id="<?= esc_attr($id) ?>"<?php checked($isActive && !$isDisabled) ?>
                    <?= $isDisabled ? 'disabled="disabled"' : '' ?>
                >
            </th>
            <td>
                <label
                    for="<?= esc_attr($id) ?>"
                    class="mlp-block-label">
                    <strong class="mlp-setting-name">
                        <?= esc_html($module->name()) ?>
                    </strong>
                    <?= esc_html($module->description()) ?>
                </label>
            </td>
        </tr>
        <?php
    }
}
