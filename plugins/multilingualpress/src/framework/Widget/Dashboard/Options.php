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

namespace Inpsyde\MultilingualPress\Framework\Widget\Dashboard;

class Options
{
    /**
     * @var array|null
     */
    private static $allOptions;

    /**
     * @var string
     */
    private $widgetId;

    /**
     * @param string $widgetId
     */
    public function __construct(string $widgetId)
    {
        $this->widgetId = $widgetId;
    }

    /**
     * Returns the options for the widget with the given ID.
     *
     * @return array
     */
    public function options(): array
    {
        return (array)($this->allOptions()[$this->widgetId] ?? []);
    }

    /**
     * Returns a specific widget option, if available.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
     */
    public function option(string $name, $default = null)
    {
        // phpcs:enable

        return $this->options()[$name] ?? $default;
    }

    /**
     * Saves an array of options for the widget with the given ID.
     *
     * @param array $options
     * @return bool
     */
    public function updateAll(array $options = []): bool
    {
        if (!$options) {
            return true;
        }

        $allOptions = $this->allOptions();

        $currentOptions = $this->options();
        $allOptions[$this->widgetId] = array_merge($currentOptions, $options);
        // Ensure next read access will come from database.
        self::$allOptions = null;

        return update_option('dashboard_widget_options', $allOptions);
    }

    /**
     * Saves a specific widget option.
     *
     * @param string $name
     * @param mixed $value
     * @return bool
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function update(string $name, $value): bool
    {
        // phpcs:enable

        return $this->updateAll([$name => $value]);
    }

    /**
     * Ensure options are stored in the static var and return them.
     *
     * @return array
     */
    private function allOptions(): array
    {
        if (!is_array(self::$allOptions)) {
            self::$allOptions = (array)(get_option('dashboard_widget_options') ?: []);
        }

        return self::$allOptions;
    }
}
