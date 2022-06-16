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

namespace Inpsyde\MultilingualPress\Core;

/**
 * Type-safe post type repository implementation.
 */
final class PostTypeRepository
{
    const DEFAULT_SUPPORTED_POST_TYPES = ['page', 'post'];
    const FIELD_ACTIVE = 'active';
    const FIELD_PERMALINK = 'permalink';
    const OPTION = 'multilingualpress_post_types';
    const FILTER_PUBLIC_POST_TYPES = 'multilingualpress.public_post_types';
    const FILTER_ALL_AVAILABLE_POST_TYPES = 'multilingualpress.all_post_types';
    const FILTER_SUPPORTED_POST_TYPES = 'multilingualpress.supported_post_types';

    /**
     * @var \WP_Post_Type[]
     */
    private $postTypes;

    /**
     * Returns all post types that MultilingualPress is able to support.
     *
     * @return \WP_Post_Type[]
     */
    public function allAvailablePostTypes(): array
    {
        if (is_array($this->postTypes)) {
            return $this->postTypes;
        }

        $postTypeNames = get_post_types(['show_ui' => true]);
        $filtered = apply_filters(self::FILTER_PUBLIC_POST_TYPES, $postTypeNames);
        if (is_array($filtered)) {
            $postTypeNames = array_filter(array_filter($filtered, 'is_string'));
        }

        // We don't support media, yet.
        $postTypeNames = array_unique(array_diff($postTypeNames, ['attachment']));

        // array_filter removes and invalid post type that could be passed via hook.
        $postTypes = array_filter(array_map('get_post_type_object', $postTypeNames));

        $postTypes and uasort(
            $postTypes,
            static function (\WP_Post_Type $left, \WP_Post_Type $right): int {
                return strcasecmp($left->labels->name, $right->labels->name);
            }
        );

        $this->postTypes = $postTypes;

        /**
         * Filter All Available Post Types
         *
         * @param array $postTypes
         * @param PostTypeRepository $this
         */
        $this->postTypes = apply_filters(
            self::FILTER_ALL_AVAILABLE_POST_TYPES,
            $this->postTypes,
            $this
        );

        return $this->postTypes;
    }

    /**
     * Returns all post types supported by MultilingualPress.
     *
     * @return string[]
     */
    public function supportedPostTypes(): array
    {
        list($found, $settings) = $this->allSettings();
        if (!$found) {
            return self::DEFAULT_SUPPORTED_POST_TYPES;
        }

        $supported = array_filter(
            $settings,
            static function (array $type): bool {
                return $type[self::FIELD_ACTIVE] ?? false;
            }
        );

        /**
         * Filter Supported Post Types
         *
         * @param array $supported
         * @param PostTypeRepository $this
         */
        $supported = apply_filters(self::FILTER_SUPPORTED_POST_TYPES, $supported, $this);

        return array_keys($supported);
    }

    /**
     * Checks if the post type with the given slug is active.
     *
     * @param string $slug
     * @return bool
     */
    public function isPostTypeActive(string $slug): bool
    {
        list($found, $value) = $this->settingFor(
            $slug,
            self::FIELD_ACTIVE,
            false
        );

        if (!$found) {
            return in_array(
                $slug,
                self::DEFAULT_SUPPORTED_POST_TYPES,
                true
            );
        }

        return (bool)$value;
    }

    /**
     * Checks if the post type with the given slug is set to be query-based.
     *
     * @param string $slug
     * @return bool
     */
    public function isPostTypeQueryBased(string $slug): bool
    {
        list(, $value) = $this->settingFor(
            $slug,
            self::FIELD_PERMALINK,
            false
        );

        return (bool)$value;
    }

    /**
     * Sets post type support according to the given settings.
     *
     * @param array $postTypes
     * @return bool
     */
    public function supportPostTypes(array $postTypes): bool
    {
        return (bool)update_network_option(
            0,
            self::OPTION,
            $postTypes
        );
    }

    /**
     * Removes the support for all post types.
     *
     * @return bool
     */
    public function removeSupportForAllPostTypes(): bool
    {
        return $this->supportPostTypes([]);
    }

    /**
     * Returns a two-items array, where the first is a boolean indicating if
     * settings are found in database, the second is actual settings array.
     * Help disguising on-purpose empty array in db from a no-result.
     *
     * @return array
     */
    private function allSettings(): array
    {
        $options = get_network_option(0, PostTypeRepository::OPTION);
        if (!is_array($options)) {
            return [false, []];
        }

        return [true, $options];
    }

    /**
     * @param string $slug
     * @param string $field
     * @param mixed $default
     * @return array|null
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    private function settingFor(
        string $slug,
        string $field,
        $default = null
    ) {

        // phpcs:enable

        list($found, $settings) = $this->allSettings();

        if (!$found) {
            return [false, $default];
        }

        return [true, $settings[$slug][$field] ?? $default];
    }
}
