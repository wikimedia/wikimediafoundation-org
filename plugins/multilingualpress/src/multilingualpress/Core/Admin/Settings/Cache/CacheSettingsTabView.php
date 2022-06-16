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

namespace Inpsyde\MultilingualPress\Core\Admin\Settings\Cache;

use Inpsyde\MultilingualPress\Framework\Admin\SettingsPageView;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

use function Inpsyde\MultilingualPress\printNonceField;

/**
 * Class CacheSettingsTabView
 * @package Inpsyde\MultilingualPress\Core\Admin
 */
class CacheSettingsTabView implements SettingsPageView
{
    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var CacheSettingsOptionsView
     */
    private $options;

    /**
     * CacheSettingsTabView constructor.
     * @param CacheSettingsOptionsView $options
     * @param Nonce $nonce
     */
    public function __construct(CacheSettingsOptionsView $options, Nonce $nonce)
    {
        $this->nonce = $nonce;
        $this->options = $options;
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->options->render();
        printNonceField($this->nonce, true);
    }
}
