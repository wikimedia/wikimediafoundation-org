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

namespace Inpsyde\MultilingualPress\Module\User\TranslationUi;

use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;

class MetaboxAction
{
    const NAME_PREFIX = 'multilingualpress';
    const TRANSLATION_META = 'multilingualpress_translation_meta';

    /**
     * Will handle the user profile field translations update
     *
     * @param int $userId The user id which is currently in edit
     * @param ServerRequest $request
     */
    public function updateTranslationData(int $userId, ServerRequest $request)
    {
        if (!current_user_can('edit_user', $userId)) {
            return;
        }

        $userTranslationValues = $request->bodyValue(
            self::NAME_PREFIX,
            INPUT_POST,
            FILTER_DEFAULT,
            FILTER_FORCE_ARRAY
        );

        if (empty($userTranslationValues)) {
            return;
        }

        update_user_meta($userId, self::TRANSLATION_META, $userTranslationValues);
    }
}
