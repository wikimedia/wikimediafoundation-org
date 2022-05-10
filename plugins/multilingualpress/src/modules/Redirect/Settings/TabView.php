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

namespace Inpsyde\MultilingualPress\Module\Redirect\Settings;

use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

use function Inpsyde\MultilingualPress\printNonceField;

/**
 * Class ModuleSettingsTabView
 * @package Inpsyde\MultilingualPress\Module\Redirect
 */
class TabView implements SettingsPageView
{
    const FILTER_VIEW_MODELS = 'multilingualpress.redirect_module_setting_models';

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var ViewRenderer
     */
    private $viewRenderers;

    /**
     * ModuleSettingsTabView constructor
     *
     * @param Nonce $nonce
     * @param ViewRenderer[] $viewRenderer
     */
    public function __construct(Nonce $nonce, ViewRenderer ...$viewRenderer)
    {
        $this->nonce = $nonce;
        $this->viewRenderers = $viewRenderer;
    }

    /**
     * Render the Settings Tab Content
     *
     * @inheritDoc
     */
    public function render()
    {
        ?>
        <table class="widefat mlp-settings-table mlp-module-redirect-settings">
            <tbody class="table-body-block">
            <?php
            /** @var ViewRenderer $viewRenderer */
            foreach ($this->viewRenderers() as $viewRenderer) : ?>
                <tr>
                    <td><?php $viewRenderer->title() ?></td>
                    <td><?php $viewRenderer->content() ?></td>
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
     * @return ViewRenderer[]
     */
    protected function viewRenderers(): array
    {
        /**
         * Filter Models
         *
         * @param ViewRenderer[] $renderers
         */
        $renderers = apply_filters(self::FILTER_VIEW_MODELS, $this->viewRenderers);
        return $this->validateViewRenderers($renderers);
    }

    /**
     * Validate View Model by Type Hint all of the models of the given collection
     *
     * @param array $models
     * @return array
     */
    protected function validateViewRenderers(array $models): array
    {
        return array_filter(
            $models,
            static function (ViewRenderer $model): bool {
                return (bool)$model;
            }
        );
    }
}
