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
 * Simple read-only storage for taxonomies active for MultilingualPress.
 */
final class ActiveTaxonomies
{
    const FILTER_ACTIVE_TAXONOMIES = 'multilingualpress.active_taxonomies';

    /**
     * @var array
     */
    private $activeTaxonomyNames;

    /**
     * Returns the allowed taxonomy names.
     *
     * @return string[]
     */
    public function names(): array
    {
        if (is_array($this->activeTaxonomyNames)) {
            return $this->activeTaxonomyNames;
        }

        /**
         * Filters the allowed taxonomies.
         *
         * @param string[] $activeTaxonomies
         */
        $activeTaxonomies = (array)apply_filters(self::FILTER_ACTIVE_TAXONOMIES, []);

        $this->activeTaxonomyNames = array_filter(
            array_unique($activeTaxonomies),
            'taxonomy_exists'
        );

        return $this->activeTaxonomyNames;
    }

    /**
     * Returns the allowed taxonomy objects.
     *
     * @return \WP_Taxonomy[]
     */
    public function objects(): array
    {
        return array_map('get_taxonomy', $this->names());
    }

    /**
     * Returns true if given taxonomy names are allowed.
     *
     * @param string[] ...$taxonomySlugs
     * @return bool
     */
    public function areTaxonomiesActive(string ...$taxonomySlugs): bool
    {
        return !array_diff(array_unique($taxonomySlugs), $this->names());
    }
}
