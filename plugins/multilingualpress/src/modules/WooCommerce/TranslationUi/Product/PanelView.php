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

namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxField;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

/**
 * Class SettingsView
 */
class PanelView
{
    /**
     * @var MetaboxField[]
     */
    private $settings;

    /**
     * SettingsView constructor.
     * @param callable ...$settings
     */
    public function __construct(callable ...$settings)
    {
        $this->settings = $settings;
    }

    /**
     * Render the settings fields.
     *
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     */
    public function render(MetaboxFieldsHelper $helper, RelationshipContext $relationshipContext)
    {
        $idAttribute = sprintf('mlp_%s_product_data', $relationshipContext->remoteSiteId());
        ?>
        <div id="<?= esc_attr($idAttribute) ?>" class="panel-wrap product_data">
            <ul class="product_data_tabs wc-tabs">
                <?php $this->renderTabs($relationshipContext) ?>
            </ul>
            <?php $this->renderSettings($helper, $relationshipContext); ?>
        </div>
        <?php
    }

    /**
     * Render setting and fields.
     *
     * @param MetaboxFieldsHelper $helper
     * @param RelationshipContext $relationshipContext
     */
    private function renderSettings(
        MetaboxFieldsHelper $helper,
        RelationshipContext $relationshipContext
    ) {

        foreach ($this->settings as $setting) {
            $setting($helper, $relationshipContext);
        }
    }

    /**
     * Render Tabs header.
     *
     * @param RelationshipContext $relationshipContext
     */
    private function renderTabs(RelationshipContext $relationshipContext)
    {
        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        foreach ($this->dataTabs() as $key => $tab) {
            $target = sprintf(
                '#mlp_%1$s_%2$s',
                $relationshipContext->remoteSiteId(),
                $tab['target']
            );

            ?>
            <li class="<?= $this->tabClassAttribute($key, $tab) ?>">
                <a href="<?= esc_url($target) ?>">
                    <span><?= esc_html($tab['label']) ?></span>
                </a>
            </li>
            <?php
        }
        // phpcs:enable
    }

    /**
     * Retrieve the data tabs settings.
     *
     * @return array
     */
    private function dataTabs(): array
    {
        $tabs = [
            'general' => [
                'label' => _x('General', 'WooCommerce product tabs', 'multilingualpress'),
                'target' => 'general_product_data',
                'class' => ['hide_if_grouped'],
                'priority' => 10,
            ],
            'inventory' => [
                'label' => _x('Inventory', 'WooCommerce product tabs', 'multilingualpress'),
                'target' => 'inventory_product_data',
                'class' => [
                    'show_if_simple',
                    'show_if_variable',
                    'show_if_grouped',
                    'show_if_external',
                ],
                'priority' => 20,
            ],
            'advanced' => [
                'label' => _x('Advanced', 'WooCommerce product tabs', 'multilingualpress'),
                'target' => 'advanced_product_data',
                'class' => [],
                'priority' => 70,
            ],
        ];

        uasort($tabs, [$this, 'sortTabs']);

        return $tabs;
    }

    /**
     * Sort the tabs based on user callback.
     *
     * @param array $left
     * @param array $right
     * @return int
     */
    private function sortTabs(array $left, array $right): int
    {
        if (!isset($left['priority'], $right['priority'])) {
            return -1;
        }

        if ($left['priority'] === $right['priority']) {
            return 0;
        }

        return $left['priority'] < $right['priority'] ? -1 : 1;
    }

    /**
     * Build the class attribute for the tab.
     *
     * @param string $key
     * @param array $tab
     * @return string
     */
    private function tabClassAttribute(string $key, array $tab): string
    {
        $tabClass = ((array)$tab['class'] ?? []);
        $classes = $tabClass;

        $classes[] = "{$key}_tab";
        $classes[] = "mlp-{$key}-settings";

        return array_reduce($classes, static function (string $stack, string $class): string {
            return $stack . ' ' . sanitize_html_class($class);
        }, '');
    }
}
