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

namespace Inpsyde\MultilingualPress\Core\Entity;

/**
 * Simple read-only storage for post types active for MultilingualPress.
 */
final class ActivePostTypes
{
    const FILTER_ACTIVE_POST_TYPES = 'multilingualpress.active_post_types';

    /**
     * @var string[]
     */
    private $activePostTypeSlugs;

    /**
     * Returns the active post type slugs.
     *
     * @return string[]
     */
    public function names(): array
    {
        if (is_array($this->activePostTypeSlugs)) {
            return $this->activePostTypeSlugs;
        }

        /**
         * Filters the active post type slugs.
         *
         * @param string[] $activePostTypes
         */
        $activePostTypes = (array)apply_filters(self::FILTER_ACTIVE_POST_TYPES, []);

        $this->activePostTypeSlugs = array_filter(
            array_unique($activePostTypes),
            'post_type_exists'
        );

        return $this->activePostTypeSlugs;
    }

    /**
     * Returns the active post type objects.
     *
     * @return \WP_Post_Type[]
     */
    public function objects(): array
    {
        return array_map('get_post_type_object', $this->names());
    }

    /**
     * Checks if all given post type slugs are active.
     *
     * @param string[] ...$postTypeSlugs
     * @return bool
     */
    public function arePostTypesActive(string ...$postTypeSlugs): bool
    {
        return !array_diff(array_unique($postTypeSlugs), $this->names());
    }
}
