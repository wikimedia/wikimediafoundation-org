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

namespace Inpsyde\MultilingualPress\Activation;

/**
 * Activator implementation using a network option.
 */
class Activator
{

    const OPTION = 'multilingualpress_activation';

    /**
     * @var callable[]
     */
    private $callbacks = [];

    /**
     * Takes care of pending plugin activation tasks.
     *
     * @return bool
     */
    public function handlePendingActivation(): bool
    {
        if (!get_network_option(0, self::OPTION)) {
            return false;
        }

        foreach ($this->callbacks as $callback) {
            $callback();
        }

        delete_network_option(0, self::OPTION);

        return true;
    }

    /**
     * Performs anything to handle the plugin activation.
     *
     * @return bool
     */
    public function handleActivation(): bool
    {
        update_network_option(0, self::OPTION, true);

        return (bool)get_network_option(0, self::OPTION);
    }

    /**
     * Registers the given callback.
     *
     * @param callable $callback
     * @param bool $prepend
     * @return Activator
     */
    public function registerCallback(callable $callback, bool $prepend = false): Activator
    {
        if ($prepend) {
            array_unshift($this->callbacks, $callback);

            return $this;
        }

        $this->callbacks[] = $callback;

        return $this;
    }
}
