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

use Inpsyde\MultilingualPress\Framework\Asset\AssetException;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingsSectionViewModel;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingView;

use function Inpsyde\MultilingualPress\isWpDebugMode;

/**
 * Site settings section view model implementation.
 */
final class SiteSettings implements SiteSettingsSectionViewModel
{
    const ID = 'mlp-site-settings';

    /**
     * @var SiteSettingView
     */
    private $view;

    /**
     * @var AssetManager
     */
    private $assetManager;

    /**
     * SiteSettings constructor.
     * @param SiteSettingView $view
     * @param AssetManager $assetManager
     */
    public function __construct(SiteSettingView $view, AssetManager $assetManager)
    {
        $this->view = $view;
        $this->assetManager = $assetManager;
    }

    /**
     * @inheritdoc
     */
    public function id(): string
    {
        return static::ID;
    }

    /**
     * @inheritdoc
     * @throws AssetException
     */
    public function renderView(int $siteId): bool
    {
        try {
            $this->assetManager->enqueueScript('multilingualpress-admin');
        } catch (AssetException $exc) {
            if (isWpDebugMode()) {
                throw $exc;
            }
        }

        return $this->view->render($siteId);
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return '';
    }
}
