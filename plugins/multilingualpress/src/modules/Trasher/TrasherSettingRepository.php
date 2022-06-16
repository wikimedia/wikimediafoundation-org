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

final class TrasherSettingRepository
{

    const META_KEY = '_trash_the_other_posts';

    /**
     * Returns the trasher setting value for the post with the given ID, or the current post.
     *
     * @param int $postId
     * @return bool
     */
    public function settingForPost(int $postId = 0): bool
    {
        return (bool)get_post_meta(
            $postId ?: get_the_ID(),
            TrasherSettingRepository::META_KEY,
            true
        );
    }

    /**
     * Updates the trasher setting value for the post with the given ID.
     *
     * @param int $postId
     * @param bool $value
     * @return bool
     */
    public function updateSetting(int $postId, bool $value): bool
    {
        return (bool)update_post_meta(
            $postId,
            TrasherSettingRepository::META_KEY,
            $value
        );
    }
}
