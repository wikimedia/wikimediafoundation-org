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

namespace Inpsyde\MultilingualPress\Framework\Filter;

/**
 * Trait for basic filter implementations.
 *
 * @see Filter
 */
trait FilterTrait
{
    /**
     * @var int
     */
    private $acceptedArgs;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var string
     */
    private $hook;

    /**
     * @var int
     */
    private $priority;

    /**
     * @return bool
     *
     * @see Filter::enable()
     */
    public function enable(): bool
    {
        $hook = $this->hook();
        if (!$hook || !$this->callback) {
            return false;
        }

        if (has_filter($hook, $this->callback)) {
            return false;
        }

        add_filter(
            $hook,
            $this->callback,
            $this->priority(),
            $this->acceptedArgs()
        );

        return true;
    }

    /**
     * @return bool
     *
     * @see Filter::disable()
     */
    public function disable(): bool
    {
        $hook = $this->hook();
        if (!$hook || !$this->callback) {
            return false;
        }

        return remove_filter($hook, $this->callback, $this->priority());
    }

    /**
     * @return string
     *
     * @see Filter::hook()
     */
    public function hook(): string
    {
        return (string)$this->hook;
    }

    /**
     * @return int
     *
     * @see Filter::priority()
     */
    public function priority(): int
    {
        return (int)($this->priority ?? Filter::DEFAULT_PRIORITY);
    }

    /**
     * @return int
     *
     * @see Filter::acceptedArgs()
     */
    public function acceptedArgs(): int
    {
        return (int)($this->acceptedArgs ?? Filter::DEFAULT_ACCEPTED_ARGS);
    }
}
