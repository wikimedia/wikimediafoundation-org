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

namespace Inpsyde\MultilingualPress\Framework\Setting;

class SettingOption implements SettingOptionInterface
{
    protected $id;

    protected $value;

    protected $callback;

    protected $label;

    protected $description;

    protected $type;

    /**
     * @param string $id The setting id
     * @param string $value The setting value
     * @param string $label The setting label
     * @param string $description The setting description
     */
    public function __construct(
        string $id,
        string $value,
        string $label,
        string $description
    ) {

        $this->id = $id;
        $this->value = $value;
        $this->label = $label;
        $this->description = $description;
    }

    /**
     * @return string The setting id
     */
    public function id(): string
    {
        return  $this->id;
    }

    /**
     * @return string The setting value
     */
    public function value(): string
    {
        return  $this->value;
    }

    /**
     * @inheritdoc
     */
    public function label(): string
    {
        return $this->label;
    }

    /**
     * @inheritdoc
     */
    public function description(): string
    {
        return $this->description;
    }
}
