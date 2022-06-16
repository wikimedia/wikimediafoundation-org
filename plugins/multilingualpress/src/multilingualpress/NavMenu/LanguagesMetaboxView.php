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

namespace Inpsyde\MultilingualPress\NavMenu;

use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

use function Inpsyde\MultilingualPress\assignedLanguageNames;

/**
 * Languages meta box view.
 */
class LanguagesMetaboxView
{
    const ID = 'mlp-languages';

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @param Nonce $nonce
     */
    public function __construct(Nonce $nonce)
    {
        $this->nonce = $nonce;
    }

    /**
     * Renders the HTML.
     */
    public function render()
    {
        ?>
        <div id="<?= esc_attr(self::ID) ?>-container">
            <?php $this->renderLanguageCheckboxes() ?>
            <?php $this->renderButtonControls() ?>
        </div>
        <?php
    }

    /**
     * Renders all language items.
     */
    private function renderLanguageCheckboxes()
    {
        $languageNames = assignedLanguageNames();
        if (!$languageNames) {
            esc_html_e('No items.', 'multilingualpress');

            return;
        }
        ?>
        <div
            id="tabs-panel-<?= esc_attr(self::ID) ?>"
            class="tabs-panel tabs-panel-active">
            <ul id="<?= esc_attr(self::ID) ?>" class="form-no-clear">
                <?php array_walk($languageNames, [$this, 'renderLanguageCheckbox']) ?>
            </ul>
        </div>
        <?php
    }

    /**
     * Renders a single item according to the given arguments.
     *
     * @param string $languageName
     * @param int $siteId
     */
    private function renderLanguageCheckbox(string $languageName, int $siteId)
    {
        ?>
        <li>
            <label class="menu-item-title">
                <input
                    type="checkbox"
                    value="<?= esc_attr((string)$siteId) ?>"
                    class="menu-item-checkbox">
                <?= esc_html($languageName) ?>
            </label>
        </li>
        <?php
    }

    /**
     * Renders the button controls HTML.
     */
    private function renderButtonControls()
    {
        $submitAttributes = [
            'id' => self::ID . '-submit',
            'data-action' => AjaxHandler::ACTION,
            'data-languages' => '#' . self::ID . ' .menu-item-checkbox',
            'data-select-all' => '#' . self::ID . '-select-all',
            'data-nonce-action' => $this->nonce->action(),
            'data-nonce' => (string)$this->nonce,
        ];

        if (empty($GLOBALS['nav_menu_selected_id'])) {
            $submitAttributes['disabled'] = 'disabled';
        }

        ?>
        <p class="button-controls wp-clearfix">
            <span class="list-controls">
                <a
                    id="<?= esc_attr(self::ID) ?>-select-all"
                    href="<?= esc_url($this->selectAllUrl()) ?>"
                    class="aria-button-if-js">
                    <?= esc_html__('Select All', 'multilingualpress') ?>
                </a>
            </span>
            <span class="add-to-menu">
                <?php
                submit_button(
                    __('Add to Menu', 'multilingualpress'),
                    'button-secondary submit-add-to-menu right',
                    'add-mlp-language-item',
                    false,
                    $submitAttributes
                );
                ?>
                <span class="spinner"></span>
            </span>
        </p>
        <?php
    }

    /**
     * Returns the URL for the "Select All" link.
     *
     * @return string
     */
    private function selectAllUrl(): string
    {
        $url = add_query_arg(
            [
                'languages-tab' => 'all',
                'selectall' => 1,
                '_wpnonce' => false,
                'action' => false,
                'customlink-tab' => false,
                'edit-menu-item' => false,
                'menu-item' => false,
                'page-tab' => false,
            ]
        );

        return "{$url}#mlp-" . self::ID;
    }
}