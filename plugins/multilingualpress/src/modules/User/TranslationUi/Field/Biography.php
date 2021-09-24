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

namespace Inpsyde\MultilingualPress\Module\User\TranslationUi\Field;

use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\Module\User\TranslationUi\MetaboxAction;

class Biography
{
    /**
     * @var string
     */
    private $key;

    /**
     * Biography field constructor.
     * @param string $key The meta key of the biography field
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Will render User Biography translation field
     *
     * @param int $userId The user id which is currently in edit
     * @param int $siteId The site id
     * @param MetaboxFieldsHelper $helper
     */
    public function render(int $userId, int $siteId, MetaboxFieldsHelper $helper)
    {
        $id = $helper->fieldId($this->key);
        $name = $helper->fieldName($this->key);
        $userTranslationMeta = get_user_meta($userId, MetaboxAction::TRANSLATION_META, true);
        $userTranslationMetaForSite = $userTranslationMeta["site-{$siteId}"] ?? [];
        $userDescriptionTranslationMetaForSite = $userTranslationMetaForSite['description'] ?? [];
        ?>
        <tr class="user-description-wrap">
            <th>
                <label for="description"><?= esc_html__('Biographical Info', 'multilingualpress');?></label>
            </th>
            <td>
                <textarea name="<?= esc_attr($name);?>" id="<?= esc_attr($id);?>" rows="5" cols="30"
                ><?php
                if (
                    !empty($userTranslationMeta) &&
                        !empty($userTranslationMetaForSite) &&
                        !empty($userDescriptionTranslationMetaForSite)
                ) {
                        echo esc_textarea($userDescriptionTranslationMetaForSite);
                }
                ?></textarea>
                <p class="description">
                    <?= esc_html__(
                        'Share a little biographical information to fill out your profile. This may be shown publicly.',
                        'multilingualpress'
                    )?>
                </p>
            </td>
        </tr>
        <?php
    }
}
