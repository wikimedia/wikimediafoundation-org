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

use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel;
use Inpsyde\MultilingualPress\Module\AltLanguageTitleInAdminBar\SettingsRepository;

use function Inpsyde\MultilingualPress\printNonceField;

/**
 * MultilingualPress "Alternative language title" site setting.
 */
final class AltLanguageTitleSiteSetting implements SiteSettingViewModel
{
    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var string
     */
    private $option;

    /**
     * @var SettingsRepository
     */
    private $repository;

    /**
     * @param string $option
     * @param Nonce $nonce
     * @param SettingsRepository $repository
     */
    public function __construct(string $option, Nonce $nonce, SettingsRepository $repository)
    {
        $this->option = $option;
        $this->nonce = $nonce;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function render(int $siteId)
    {
        ?>
        <input
            type="text"
            name="<?= esc_attr(SettingsRepository::OPTION_SITE) ?>"
            value="<?= esc_attr($this->repository->alternativeLanguageTitle($siteId)) ?>"
            class="regular-text"
            id="<?= esc_attr($this->option) ?>">
        <p class="description">
            <?php
            esc_html_e(
                'Enter the title you want to see in the admin bar instead of the default one (i.e. "My English Site").',
                'multilingualpress'
            );
            ?>
        </p>
        <?php
        printNonceField($this->nonce);
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return sprintf(
            '<label for="%2$s">%1$s</label>',
            esc_html__('Alternative language title', 'multilingualpress'),
            esc_attr($this->option)
        );
    }
}
