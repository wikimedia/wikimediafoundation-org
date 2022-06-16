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

namespace Inpsyde\MultilingualPress\Framework\Setting\User;

/**
 * User setting view.
 */
class UserSettingView
{
    /**
     * @var bool
     */
    private $checkUser;

    /**
     * @var UserSettingViewModel
     */
    private $model;

    /**
     * @param UserSettingViewModel $model
     * @param bool $checkUser
     */
    public function __construct(UserSettingViewModel $model, bool $checkUser = true)
    {
        $this->model = $model;
        $this->checkUser = $checkUser;
    }

    /**
     * Renders the user setting markup.
     *
     * @param \WP_User $user
     * @return bool
     */
    public function render(\WP_User $user): bool
    {
        if ($this->checkUser && !current_user_can('edit_user', $user->ID)) {
            return false;
        }
        ?>
        <tr>
            <th scope="row">
                <?= wp_kses_post($this->model->title()) ?>
            </th>
            <td>
                <?php $this->model->render($user) ?>
            </td>
        </tr>
        <?php

        return true;
    }
}
