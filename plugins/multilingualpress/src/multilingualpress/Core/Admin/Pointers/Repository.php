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

namespace Inpsyde\MultilingualPress\Core\Admin\Pointers;

/**
 * Pointers Repository.
 */
class Repository
{
    /**
     * @var array
     */
    private $pointers;

    /**
     * @var array
     */
    private $actions;

    /**
     * @param string $screen
     * @param string $key
     * @param string $target
     * @param string $next
     * @param array $nextTrigger
     * @param array $options
     * @return $this
     */
    public function registerForScreen(
        string $screen,
        string $key,
        string $target,
        string $next,
        array $nextTrigger,
        array $options
    ): Repository {

        $this->pointers[$screen][$key] = [
            'target' => $target,
            'next' => $next,
            'next_trigger' => $nextTrigger,
            'options' => $options,
        ];

        return $this;
    }

    /**
     * @param string $screen
     * @param string $action
     * @return $this
     */
    public function registerActionForScreen(string $screen, string $action): Repository
    {
        $this->actions[$screen] = $action;

        return $this;
    }

    /**
     * @param string $screen
     * @return array
     */
    public function forScreen(string $screen): array
    {
        return [
            $this->pointers[$screen] ?? [],
            $this->actions[$screen] ?? '',
        ];
    }
}
