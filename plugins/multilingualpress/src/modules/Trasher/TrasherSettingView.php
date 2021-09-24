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

namespace Inpsyde\MultilingualPress\Module\Trasher;

use Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

use function Inpsyde\MultilingualPress\printNonceField;

/**
 * Trasher setting view.
 */
class TrasherSettingView
{
    /**
     * @var ActivePostTypes
     */
    private $activePostTypes;

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var TrasherSettingRepository
     */
    private $settingRepository;

    /**
     * @param TrasherSettingRepository $settingRepository
     * @param Nonce $nonce
     * @param ActivePostTypes $activePostTypes
     */
    public function __construct(
        TrasherSettingRepository $settingRepository,
        Nonce $nonce,
        ActivePostTypes $activePostTypes
    ) {

        $this->settingRepository = $settingRepository;
        $this->nonce = $nonce;
        $this->activePostTypes = $activePostTypes;
    }

    /**
     * Renders the setting markup.
     *
     * @param \WP_Post $post
     */
    public function render(\WP_Post $post)
    {
        if (!$this->activePostTypes->arePostTypesActive((string)$post->post_type)) {
            return;
        }

        ?>
        <div class="misc-pub-section misc-pub-mlp-trasher">
            <?php printNonceField($this->nonce) ?>
            <label for="mlp-trasher">
                <input
                    type="checkbox"
                    name="<?= esc_attr(TrasherSettingRepository::META_KEY) ?>"
                    value="1"
                    id="mlp-trasher"
                    <?php checked($this->settingRepository->settingForPost((int)$post->ID)) ?>>
                <?php
                esc_html_e(
                    'Send all the translations to trash when this post is trashed.',
                    'multilingualpress'
                );
                ?>
            </label>
        </div>
        <?php
    }
}
