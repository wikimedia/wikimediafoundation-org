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

// phpcs:enable
use WP_Taxonomy;

// phpcs:disable WordPress.PHP.StrictInArray.MissingArguments

/**
 * Class TaxonomyRepository
 * @package Inpsyde\MultilingualPress\Core
 */
class TaxonomyRepository implements SettingsRepository
{
    public const DEFAULT_SUPPORTED_TAXONOMIES = [
        'category',
        'post_tag',
    ];
    const FIELD_ACTIVE = 'active';
    const FIELD_SKIN = 'ui';
    const OPTION = 'multilingualpress_taxonomies';
    const FILTER_ALL_AVAILABLE_TAXONOMIES = 'multilingualpress.all_taxonomies';
    const FILTER_SUPPORTED_TAXONOMIES = 'multilingualpress.supported_taxonomies';

    /**
     * @var WP_Taxonomy[]
     */
    private $allAvailableTaxonomies;

    /**
     * Returns all taxonomies that MultilingualPress is able to support.
     *
     * @return WP_Taxonomy[]
     */
    public function allAvailableTaxonomies(): array
    {
        if (is_array($this->allAvailableTaxonomies)) {
            return $this->allAvailableTaxonomies;
        }

        $this->allAvailableTaxonomies = $this->allAllowedTaxonomies();
        if ($this->allAvailableTaxonomies) {
            uasort(
                $this->allAvailableTaxonomies,
                static function (WP_Taxonomy $left, WP_Taxonomy $right): int {
                    return strcasecmp($left->labels->name, $right->labels->name);
                }
            );
        }

        /**
         * Filter All Available Taxonomies
         *
         * @param array $allTaxonomies
         * @param TaxonomyRepository $this
         */
        $this->allAvailableTaxonomies = apply_filters(
            self::FILTER_ALL_AVAILABLE_TAXONOMIES,
            $this->allAvailableTaxonomies,
            $this
        );

        return $this->allAvailableTaxonomies;
    }

    /**
     * Returns the UI ID of the taxonomy with the given slug.
     *
     * @param string $slug
     * @return string
     */
    public function taxonomySkinId(string $slug): string
    {
        list(, $value) = $this->settingFor(
            $slug,
            self::FIELD_SKIN,
            false
        );

        return (string)$value;
    }

    /**
     * Returns all taxonomies supported by MultilingualPress.
     *
     * @return string[]
     */
    public function supportedTaxonomies(): array
    {
        list($found, $settings) = $this->allSettings();

        if (!$found) {
            return [];
        }

        $supported = array_filter(
            $settings,
            static function (array $data): bool {
                return $data[self::FIELD_ACTIVE] ?? false;
            }
        );

        /**
         * Filter Supported Taxonomies
         *
         * @param array $supported
         * @param TaxonomyRepository $this
         */
        $supported = apply_filters(self::FILTER_SUPPORTED_TAXONOMIES, $supported, $this);

        return array_keys($supported);
    }

    /**
     * Checks if the taxonomy with the given slug is active.
     *
     * @param string $slug
     * @return bool
     */
    public function isTaxonomyActive(string $slug): bool
    {
        list($found, $value) = $this->settingFor(
            $slug,
            self::FIELD_ACTIVE,
            false
        );

        return (bool)$value;
    }

    /**
     * Sets taxonomy support according to the given settings.
     *
     * @param array $taxonomies
     * @return bool
     */
    public function supportTaxonomies(array $taxonomies): bool
    {
        return (bool)update_network_option(
            0,
            self::OPTION,
            $taxonomies
        );
    }

    /**
     * Removes the support for all taxonomies.
     *
     * @return bool
     */
    public function removeSupportForAllTaxonomies(): bool
    {
        return $this->supportTaxonomies([]);
    }

    /**
     * Retrieve all Registered Taxonomies
     *
     * @return WP_Taxonomy[]
     */
    protected function allAllowedTaxonomies(): array
    {
        $allTaxonomies = get_taxonomies(['show_ui' => true], 'objects');
        if (!get_option('link_manager_enabled')) {
            $allTaxonomies = array_filter(
                $allTaxonomies,
                // phpcs:ignore Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
                static function ($taxonomy): bool {
                    return $taxonomy instanceof WP_Taxonomy and $taxonomy->name !== 'link_category';
                }
            );
        }

        return $allTaxonomies;
    }

    /**
     * @inheritDoc
     */
    public function allSettings(): array
    {
        $options = get_network_option(0, self::OPTION);
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
