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

namespace Inpsyde\MultilingualPress\Framework\Module;

final class Module
{

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $id;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $disabled;

    /**
     * @param string $id
     * @param array $data
     */
    public function __construct(string $id, array $data = [])
    {
        $this->id = $id;
        $this->description = (string)($data['description'] ?? '');
        $this->isActive = (bool)($data['active'] ?? false);
        $this->name = (string)($data['name'] ?? '');
        $this->disabled = (bool)($data['disabled'] ?? false);
    }

    /**
     * Activates the module.
     *
     * @return Module
     */
    public function activate(): Module
    {
        $this->isActive = true;

        return $this;
    }

    /**
     * Deactivates the module.
     *
     * @return Module
     */
    public function deactivate(): Module
    {
        $this->isActive = false;

        return $this;
    }

    /**
     * Returns the description of the module.
     *
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * Returns the ID of the module.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Checks if the module is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * Module is not able to be activated
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * Returns the name of the module.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
}
