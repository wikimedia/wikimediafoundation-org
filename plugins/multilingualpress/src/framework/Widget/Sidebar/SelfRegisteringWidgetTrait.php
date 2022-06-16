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

namespace Inpsyde\MultilingualPress\Framework\Widget\Sidebar;

/**
 * Trait to be used by all self-registering widget implementations.
 *
 * @see Widget
 */
trait SelfRegisteringWidgetTrait
{

    /**
     * Registers the widget.
     *
     * @return bool
     */
    public function register(): bool
    {
        if (did_action('widgets_init')) {
            return false;
        }

        add_action(
            'widgets_init',
            function () {
                /** @var \WP_Widget $this */
                register_widget($this);
            }
        );

        return true;
    }
}
