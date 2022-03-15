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

namespace Inpsyde\MultilingualPress\Module\Redirect;

use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use Inpsyde\MultilingualPress\Framework\Setting\SettingOptionInterface;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel;
use Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository;

use function Inpsyde\MultilingualPress\printNonceField;

/**
 * Redirect site setting.
 */
class RedirectSiteSettings implements SiteSettingViewModel
{
    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var array<SettingOptionInterface>
     */
    private $options;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param array<SettingOptionInterface> $options
     * @param Nonce $nonce
     * @param Repository $repository
     */
    public function __construct(array $options, Nonce $nonce, Repository $repository)
    {
        $this->options = $options;
        $this->nonce = $nonce;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function render(int $siteId)
    {
        foreach ($this->options as $option) {
            $value = $this->repository->isRedirectSettingEnabledForSite($siteId, $option->value())
            ?>
            <p class="<?= esc_attr($option->id()) ?>">
                <label for="<?= esc_attr($option->id()) ?>">
                    <input
                            type="checkbox"
                            name="<?= esc_attr($this->repository::OPTION_SITE) ?>[]"
                            value="<?= esc_attr($option->value()) ?>"
                            id="<?= esc_attr($option->id()) ?>"
                        <?php checked($value) ?>>
                    <?= esc_html($option->label()) ?>
                </label>
            </p>
            <?php
        }
        printNonceField($this->nonce);
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return sprintf(
            '<label for="redirect">%1$s</label>',
            esc_html__('Redirect', 'multilingualpress')
        );
    }
}
