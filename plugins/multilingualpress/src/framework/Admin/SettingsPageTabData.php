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

namespace Inpsyde\MultilingualPress\Framework\Admin;

/**
 * Settings page tab data structure.
 */
final class SettingsPageTabData implements SettingsPageTabDataAccess
{
    /**
     * @var string
     */
    private $capability;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $title;

    /**
     * @param string $id
     * @param string $title
     * @param string $slug
     * @param string $capability
     */
    public function __construct(
        string $id,
        string $title,
        string $slug,
        string $capability = ''
    ) {

        $this->capability = $capability;
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
    }

    /**
     * @inheritdoc
     */
    public function capability(): string
    {
        return $this->capability;
    }

    /**
     * @inheritdoc
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function slug(): string
    {
        return $this->slug;
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return $this->title;
    }
}
