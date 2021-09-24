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

namespace Inpsyde\MultilingualPress\Module\QuickLinks\Settings;

use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use Inpsyde\MultilingualPress\Module\QuickLinks\Model\ViewModel;

use function Inpsyde\MultilingualPress\printNonceField;

/**
 * Class TabView
 * @package Inpsyde\MultilingualPress\Module\QuickLinks\Settings
 */
class TabView implements SettingsPageView
{
    const FILTER_VIEW_MODELS = 'multilingualpress.quicklinks_module_setting_models';

    /**
     * @var Nonce
     */

    private $nonce;

    /**
     * @var ViewModel
     */
    private $viewModels;

    /**
     * ModuleSettingsTabView constructor
     *
     * @param Nonce $nonce
     * @param ViewModel[] $viewModels
     */
    public function __construct(Nonce $nonce, ViewModel ...$viewModels)
    {
        $this->nonce = $nonce;
        $this->viewModels = $viewModels;
    }

    /**
     * Render the Settings Tab Content
     *
     * @inheritDoc
     */
    public function render()
    {
        ?>
        <table class="widefat mlp-settings-table mlp-module-quicklink-settings">
            <tbody class="table-body-block">
            <?php
            /** @var ViewModel $viewModel */
            foreach ($this->viewModels() as $viewModel) : ?>
                <tr id="<?= esc_attr($viewModel->id()) ?>">
                    <td><?php $viewModel->title() ?></td>
                    <td><?php $viewModel->render() ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        printNonceField($this->nonce);
    }

    /**
     * Retrieve the Models
     *
     * @return ViewModel[]
     */
    protected function viewModels(): array
    {
        /**
         * Filter Models
         *
         * @param ViewModel[] $models
         */
        $models = apply_filters(self::FILTER_VIEW_MODELS, $this->viewModels);

        return $this->validateViewModels($models);
    }

    /**
     * Validate View Model by Type Hint all of the models of the given collection
     *
     * @param array $models
     * @return array
     */
    protected function validateViewModels(array $models): array
    {
        return array_filter(
            $models,
            static function (ViewModel $model): bool {
                return (bool)$model;
            }
        );
    }
}
