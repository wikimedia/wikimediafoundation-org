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

namespace Inpsyde\MultilingualPress\Core\Admin;

use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView;

/**
 * New site settings section view model implementation.
 */
final class NewSiteSettings implements SiteSettingsSectionViewModel
{
    const SECTION_ID = 'mlp-new-site-settings';

    /**
     * @var SiteSettingView
     */
    private $view;

    /**
     * @param SiteSettingView $view
     */
    public function __construct(SiteSettingView $view)
    {
        $this->view = $view;
    }

    /**
     * @inheritdoc
     */
    public function id(): string
    {
        return static::SECTION_ID;
    }

    /**
     * @inheritdoc
     */
    public function renderView(int $siteId): bool
    {
        return $this->view->render($siteId);
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return sprintf(
            '<h2>%s</h2>',
            esc_html__('MultilingualPress', 'multilingualpress')
        );
    }
}
