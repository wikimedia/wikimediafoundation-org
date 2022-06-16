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

namespace Inpsyde\MultilingualPress\Module\WooCommerce;

/**
 * Class AvailableTaxonomiesAttributes
 */
class AvailableTaxonomiesAttributes
{
    /**
     * Remove Attributes from the list of the translatable taxonomies in the settings ui
     *
     * @param array $taxonomies
     * @return array
     */
    public function removeAttributes(array $taxonomies): array
    {
        $keys = array_keys($taxonomies);
        $toRemove = array_intersect($keys, $this->attributes());

        foreach ($toRemove as $attribute) {
            unset($taxonomies[$attribute]);
        }

        return $taxonomies;
    }

    /**
     * Retrieve WooCommerce attributes taxonomies names
     *
     * @return array
     */
    private function attributes(): array
    {
        $attributes = wp_list_pluck(wc_get_attribute_taxonomies(), 'attribute_name');
        $attributes = array_map('wc_attribute_taxonomy_name', $attributes);

        return $attributes;
    }
}
