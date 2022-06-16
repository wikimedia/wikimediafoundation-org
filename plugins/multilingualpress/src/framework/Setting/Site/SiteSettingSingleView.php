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

namespace Inpsyde\MultilingualPress\Framework\Setting\Site;

/**
 * Site setting view implementation for a single setting.
 */
final class SiteSettingSingleView implements SiteSettingView
{

    /**
     * @var bool
     */
    private $checkUser;

    /**
     * @var SiteSettingViewModel
     */
    private $model;

    /**
     * @param SiteSettingViewModel $model
     * @param bool $checkUser
     */
    public function __construct(SiteSettingViewModel $model, bool $checkUser = true)
    {
        $this->model = $model;
        $this->checkUser = $checkUser;
    }

    /**
     * @inheritdoc
     */
    public function render(int $siteId): bool
    {
        if ($this->checkUser && !current_user_can('manage_sites')) {
            return false;
        }
        ?>
        <tr class="form-field">
            <th scope="row">
                <?= wp_kses_post($this->model->title()) ?>
            </th>
            <td>
                <?php $this->model->render($siteId) ?>
            </td>
        </tr>
        <?php

        return true;
    }
}
