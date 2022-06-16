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

namespace Inpsyde\MultilingualPress\Framework;

/**
 * Storage for the (switched) state of the network.
 */
class NetworkState
{
    /**
     * @var int
     */
    private $siteId;

    /**
     * @var int[]
     */
    private $stack;

    /**
     * Returns a new instance for the global site ID and switched stack.
     *
     * @return static
     */
    public static function create(): NetworkState
    {
        $state = new static();
        $state->siteId = get_current_blog_id();
        $state->stack = (array)($GLOBALS['_wp_switched_stack'] ?? []);

        return $state;
    }

    private function __construct()
    {
    }

    /**
     * Restores the stored site state.
     *
     * @return int
     */
    public function restore(): int
    {
        switch_to_blog($this->siteId);
        $GLOBALS['_wp_switched_stack'] = $this->stack;
        $GLOBALS['switched'] = (bool)$this->stack;

        return get_current_blog_id();
    }
}
