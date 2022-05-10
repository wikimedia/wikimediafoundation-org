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

use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageTab;
use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use UnexpectedValueException;

use function Inpsyde\MultilingualPress\printNonceField;

/**
 * Plugin settings page view.
 */
final class PluginSettingsPageView implements SettingsPageView
{
    const QUERY_ARG_TAB = 'tab';

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var ServerRequest
     */
    private $request;

    /**
     * @var array
     */
    private $settingTabs;

    /**
     * @param Nonce $nonce
     * @param ServerRequest $request
     * @param array $settingTabs
     * @throws \UnexpectedValueException
     */
    public function __construct(Nonce $nonce, ServerRequest $request, array $settingTabs)
    {
        $this->assertSettingTabs($settingTabs);

        $this->nonce = $nonce;
        $this->request = $request;
        $this->settingTabs = $settingTabs;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        ?>
        <div class="wrap">
            <h1><?= esc_html(get_admin_page_title()) ?></h1>
            <?php settings_errors() ?>
            <?php $this->renderForm() ?>
        </div>
        <?php
    }

    /**
     * Returns the slug of the active tab.
     *
     * @return string
     */
    private function currentlyActiveTab(): string
    {
        static $activeTab;
        if ($activeTab === null) {
            $tab = (string)$this->request->bodyValue(self::QUERY_ARG_TAB, INPUT_GET);
            $activeTab = $tab && array_key_exists($tab, $this->settingTabs)
                ? $tab
                : key($this->settingTabs);
        }

        return $activeTab;
    }

    /**
     * Renders the active tab content.
     */
    private function renderContent()
    {
        $this->settingTabs[$this->currentlyActiveTab()]->view()->render();
    }

    /**
     * Renders the form.
     */
    private function renderForm()
    {
        if (!$this->settingTabs) {
            return;
        }

        $this->renderTabs();
        $url = admin_url('admin-post.php?action=' . PluginSettingsUpdater::ACTION)
        ?>
        <form
            method="post"
            action="<?= esc_url($url) ?>"
            id="multilingualpress-modules">
            <?php
            printNonceField($this->nonce);
            $this->renderContent();

            $tab = (string)$this->request->bodyValue(self::QUERY_ARG_TAB, INPUT_GET);
            if ($tab !== 'license') {
                submit_button(__('Save Changes', 'multilingualpress'));
            }
            ?>
        </form>
        <?php
    }

    /**
     * Renders the tabbed navigation.
     */
    private function renderTabs()
    {
        ?>
        <h2 class="nav-tab-wrapper wp-clearfix">
            <?php
            array_walk(
                $this->settingTabs,
                [$this, 'renderTab'],
                $this->currentlyActiveTab()
            );
            ?>
        </h2>
        <?php
    }

    /**
     * Renders the given tab.
     *
     * @param SettingsPageTab $tab
     * @param string $slug
     * @param string $active
     */
    private function renderTab(
        SettingsPageTab $tab,
        string $slug,
        string $active
    ) {

        $url = add_query_arg(self::QUERY_ARG_TAB, $slug);
        $class = 'nav-tab';
        if ($active === $slug) {
            $class .= ' nav-tab-active';
        }
        ?>
        <a
            href="<?= esc_url($url); ?>"
            id="<?= esc_attr($tab->id()) ?>"
            class="<?= esc_attr($class) ?>">
            <?= esc_html($tab->title()) ?>
        </a>
        <?php
    }

    /**
     * Ensure the given array contains items that are instances of SettingsPageTab
     *
     * @param array $settingsTab
     * @throws UnexpectedValueException
     */
    private function assertSettingTabs(array $settingsTab)
    {
        foreach ($settingsTab as $tab) {
            if (!$tab instanceof SettingsPageTab) {
                throw new UnexpectedValueException(
                    'All setting tabs have to be instance of SettingsPageTab'
                );
            }
        }
    }
}
