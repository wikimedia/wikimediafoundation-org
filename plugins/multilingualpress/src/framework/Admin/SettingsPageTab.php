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
 * Settings page tab.
 */
class SettingsPageTab implements SettingsPageTabDataAccess
{

    /**
     * @var SettingsPageTabDataAccess
     */
    private $data;

    /**
     * @var SettingsPageView
     */
    private $view;

    /**
     * @param SettingsPageTabDataAccess $data
     * @param SettingsPageView $view
     */
    public function __construct(SettingsPageTabDataAccess $data, SettingsPageView $view)
    {
        $this->data = $data;
        $this->view = $view;
    }

    /**
     * @inheritdoc
     */
    public function capability(): string
    {
        return $this->data->capability();
    }

    /**
     * @inheritdoc
     */
    public function data(): SettingsPageTabDataAccess
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function id(): string
    {
        return $this->data->id();
    }

    /**
     * @inheritdoc
     */
    public function slug(): string
    {
        return $this->data->slug();
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return $this->data->title();
    }

    /**
     * Returns the view object.
     *
     * @return SettingsPageView
     */
    public function view(): SettingsPageView
    {
        return $this->view;
    }
}
