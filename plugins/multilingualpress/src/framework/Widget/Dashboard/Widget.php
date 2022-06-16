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

class Widget
{

    /**
     * @var array
     */
    private $callbackArgs;

    /**
     * @var string
     */
    private $capability;

    /**
     * @var callable|null
     */
    private $controlCallback;

    /**
     * @var string
     */
    private $widgetId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var View
     */
    private $view;

    /**
     * @param string $widgetId
     * @param string $widgetName
     * @param View $view
     * @param string $capability
     * @param array $callbackArgs
     * @param callable|null $controlCallback
     */
    public function __construct(
        string $widgetId,
        string $widgetName,
        View $view,
        string $capability = '',
        array $callbackArgs = [],
        callable $controlCallback = null
    ) {

        $this->widgetId = $widgetId;
        $this->name = $widgetName;
        $this->view = $view;
        $this->capability = $capability;
        $this->callbackArgs = $callbackArgs;
        $this->controlCallback = $controlCallback;
    }

    /**
     * Registers the widget.
     *
     * @return bool
     */
    public function register(): bool
    {
        if ($this->capability && !current_user_can($this->capability)) {
            return false;
        }

        add_action(
            'wp_dashboard_setup',
            function () {
                wp_add_dashboard_widget(
                    $this->widgetId,
                    $this->name,
                    [$this->view, 'render'],
                    $this->controlCallback,
                    $this->callbackArgs
                );
            }
        );

        return true;
    }
}
