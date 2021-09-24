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
use Inpsyde\MultilingualPress\Framework\Setting\User\UserSettingViewModel;
use Inpsyde\MultilingualPress\Module\Redirect\Settings\Repository;

use function Inpsyde\MultilingualPress\printNonceField;

/**
 * Redirect user setting.
 */
final class RedirectUserSetting implements UserSettingViewModel
{
    /**
     * @var string
     */
    private $userMetaKey;

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param string $userMetaKey
     * @param Nonce $nonce
     * @param Repository $repository
     */
    public function __construct(
        string $userMetaKey,
        Nonce $nonce,
        Repository $repository
    ) {

        $this->userMetaKey = $userMetaKey;
        $this->nonce = $nonce;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function render(\WP_User $user)
    {
        ?>
        <label for="<?= esc_attr($this->userMetaKey) ?>">
            <input
                    type="checkbox"
                    name="<?= esc_attr($this->userMetaKey) ?>"
                    value="1"
                    id="<?= esc_attr($this->userMetaKey) ?>"
                <?php checked($this->repository->isRedirectEnabledForUser((int)$user->ID)) ?>>
            <?php
            esc_html_e(
                'Do not redirect me to the best matching language version.',
                'multilingualpress'
            );
            ?>
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
            esc_attr($this->userMetaKey)
        );
    }
}
