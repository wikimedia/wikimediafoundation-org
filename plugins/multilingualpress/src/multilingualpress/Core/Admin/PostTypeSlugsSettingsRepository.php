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

class PostTypeSlugsSettingsRepository
{
    const POST_TYPE_SLUGS = 'mlp_site_post_type_slugs';
    const OPTION = 'multilingualpress_post_type_slugs_translation';

    /**
     * Retrieve the post type slugs for the site with the given ID.
     *
     * @param int|null $siteId
     * @return array
     */
    public function postTypeSlugs(int $siteId = null): array
    {
        $siteId = $siteId ?: get_current_blog_id();

        $settings = $this->allSettings();
        $slugs = $settings[$siteId] ?? [];

        foreach ($slugs as &$slug) {
            $slug = sanitize_text_field($slug);
        }

        return $slugs;
    }

    /**
     * Update the post type slugs for the site with the given ID.
     *
     * @param array $slugs
     * @param int|null $siteId
     * @return bool
     */
    public function updatePostTypeSlugs(array $slugs, int $siteId = null): bool
    {
        return $this->updateSetting($slugs, $siteId);
    }

    /**
     * @return array
     */
    private function allSettings(): array
    {
        return (array)get_network_option(0, self::OPTION, []);
    }

    /**
     * @param array $slugs
     * @param int|null $siteId
     * @return bool
     */
    private function updateSetting(array $slugs, int $siteId = null): bool
    {
        $siteId = $siteId ?: get_current_blog_id();
        $settings = $this->allSettings();

        if (!isset($settings[$siteId])) {
            $settings[$siteId] = [];
        }

        $settings[$siteId] = $slugs;

        return update_network_option(0, self::OPTION, $settings);
    }
}
