<?php # -*- coding: utf-8 -*-
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
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel;

use Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository;
use function Inpsyde\MultilingualPress\printNonceField;

/**
 * Redirect site setting.
 */
class RedirectSiteSetting implements SiteSettingViewModel
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
     * @var Repository
     */
    private $repository;

    /**
     * @param string $option
     * @param Nonce $nonce
     * @param Repository $repository
     */
    public function __construct(string $option, Nonce $nonce, Repository $repository)
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
        <label for="<?= esc_attr($this->option) ?>">
            <input
                    type="checkbox"
                    name="<?= esc_attr($this->option) ?>"
                    value="1"
                    id="<?= esc_attr($this->option) ?>"
                <?php checked($this->repository->isRedirectEnabledForSite($siteId)) ?>>
            <?php esc_html_e('Enable automatic redirect', 'multilingualpress') ?>
        </label>
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
            esc_html__('Redirect', 'multilingualpress'),
            esc_attr($this->option)
        );
    }
}
