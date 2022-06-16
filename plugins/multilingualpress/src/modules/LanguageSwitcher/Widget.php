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

namespace Inpsyde\MultilingualPress\Module\LanguageSwitcher;

use Inpsyde\MultilingualPress\Flags\Flag\Flag;
use Inpsyde\MultilingualPress\Framework\Module\ModuleManager;
use Inpsyde\MultilingualPress\SiteFlags\ServiceProvider as SiteFlags;

class Widget extends \WP_Widget
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var View
     */
    private $view;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @param Model $model
     * @param View $view
     * @param ModuleManager $moduleManager
     */
    public function __construct(Model $model, View $view, ModuleManager $moduleManager)
    {
        $widgetOptions = [
            'classname' => 'multilingualpress_language_switcher',
            'description' => esc_html__('Language Switcher', 'multilingualpress'),
        ];

        parent::__construct(
            'multilingualpress_language_switcher',
            esc_html__('Language Switcher', 'multilingualpress'),
            $widgetOptions
        );

        $this->model = $model;
        $this->view = $view;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     * @return void
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     */
    public function widget($args, $instance)
    {
        // phpcs:enable

        echo wp_kses_post($args['before_widget']);

        $data = $this->model->data($args, $instance);
        $this->view->render($data);

        echo wp_kses_post($args['after_widget']);
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance
     * @return void
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    public function form($instance)
    {
        // phpcs:enable

        $title = $instance['title'] ?? '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_attr_e('Title:', 'multilingualpress'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <?php
            $showLinks = !empty($instance['show_links_for_translated_content_only']);
            $id = $this->get_field_id('show_links_for_translated_content_only');
            $name = $this->get_field_name('show_links_for_translated_content_only');
            ?>
            <label for="<?php echo esc_attr($id); ?>">
                <input type="checkbox" name="<?php echo esc_attr($name); ?>" value="1"
                       id="<?php echo esc_attr($id); ?>"<?php checked($showLinks); ?>>
                <?php esc_html_e(
                    'Show links for translated content only',
                    'multilingualpress'
                ); ?>
            </label>
        </p>

        <p>
            <?php
            $showCurrentSite = !empty($instance['show_current_site'])
                ? (int)$instance['show_current_site']
                : 0;
            $id = $this->get_field_id('show_current_site');
            $name = $this->get_field_name('show_current_site');
            ?>
            <label for="<?php echo esc_attr($id); ?>">
                <input type="checkbox" name="<?php echo esc_attr($name); ?>" value="1"
                       id="<?php echo esc_attr($id); ?>"<?php checked($showCurrentSite); ?>>
                <?php esc_html_e(
                    'Show current site',
                    'multilingualpress'
                ); ?>
            </label>
        </p>

        <p>
            <?php
            $languageName = $instance['language_name'] ?? 'isoName';
            $id = $this->get_field_id('language_name');
            $name = $this->get_field_name('language_name');
            ?>
            <label for="<?= esc_attr($id); ?>">
                <?php esc_html_e('Choose how to show the language name:', 'multilingualpress'); ?>
            </label>
            <select id="<?= esc_attr($id); ?>" name="<?= esc_attr($name); ?>">
                <?php
                $languageNames = [
                    'isoName' => __('Language ISO Name', 'multilingualpress'),
                    'locale' => __('Language Locale', 'multilingualpress'),
                    'name' => __('Language Name', 'multilingualpress'),
                    'isoCode' => __('Language ISO Code', 'multilingualpress'),
                ];

                foreach ($languageNames as $key => $name) {
                    ?>
                    <option value="<?= esc_attr($key)?>" id="<?= esc_attr($key)?>"
                        <?= selected($languageName, $key, false)?>><?= esc_html($name); ?>
                    </option>

                <?php } ?>
            </select>
        </p>

        <?php
        if ($this->isShowFlagOption()) {
            $showFlags = !empty($instance['show_flags']); ?>
            <p>
                <?php
                $id = $this->get_field_id('show_flags');
                $name = $this->get_field_name('show_flags');
                ?>
                <label for="<?php echo esc_attr($id); ?>">
                    <input type="checkbox" name="<?php echo esc_attr($name); ?>" value="1"
                           id="<?php echo esc_attr($id); ?>"<?php checked($showFlags); ?>>
                    <?php esc_html_e(
                        'Show flags',
                        'multilingualpress'
                    ); ?>
                </label>
            </p>
        <?php }
    }

    /**
     * Processing widget options on save
     *
     * @param array $newInstance
     * @param array $oldInstance
     * @return array|void
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    public function update($newInstance, $oldInstance)
    {
        // phpcs:enable

        $instance['title'] = (!empty($newInstance['title']))
            ? sanitize_text_field($newInstance['title'])
            : '';

        $instance['show_links_for_translated_content_only'] = (int)$newInstance['show_links_for_translated_content_only'] ?? 0;

        $instance['show_current_site'] = (int)$newInstance['show_current_site'] ?? 0;

        $instance['language_name'] = isset($newInstance['language_name'])
            ? wp_strip_all_tags($newInstance['language_name'])
            : '';

        if ($this->isShowFlagOption()) {
            $instance['show_flags'] = (int)$newInstance['show_flags'] ?? 0;
        }

        return $instance;
    }

    /**
     * Whether to show the site flags option
     *
     * The "Show Flags" option should be shown if the old version of Site Flags addon is active or
     * if the new Site Flags module is enabled
     *
     * @return bool
     */
    protected function isShowFlagOption(): bool
    {
        return interface_exists(Flag::class) || $this->moduleManager->isModuleActive(SiteFlags::MODULE_ID);
    }
}
