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
 * Class PermalinkStructure
 */
class PermalinkStructure
{
    /**
     * Get the base permalink structure for product by WooCommerce Settings
     *
     * @return string
     */
    public function baseforProduct(): string
    {
        $productBase = $this->wooCommercePermalinks()->product_base;
        return ($this->permalinksStructure() && $productBase ? $productBase : '');
    }

    /**
     * Get the base permalink structure for product category by WooCommerce Settings
     *
     * @return string
     */
    public function forProductCategory(): string
    {
        $categoryBase = $this->wooCommercePermalinks()->category_base;
        return ($this->permalinksStructure() && $categoryBase ? $categoryBase : '');
    }

    /**
     * Get the base permalink structure for product tag by WooCommerce Settings
     *
     * @return string
     */
    public function forProductTag(): string
    {
        $tagBase = $this->wooCommercePermalinks()->tag_base;
        return ($this->permalinksStructure() && $tagBase ? $tagBase : '');
    }

    /**
     * Get the base permalink structure for product attribute by WooCommerce Settings
     *
     * @param string $taxonomySlug
     * @return string
     */
    public function forProductAttribute(string $taxonomySlug): string
    {
        $attribute = $this->attributeNameByTaxonomySlug($taxonomySlug);

        $attributeBase = trailingslashit($this->wooCommercePermalinks()->attribute_base);
        $basePermalink = "{$attributeBase}{$attribute}";

        return $this->permalinksStructure() ? $basePermalink : $taxonomySlug;
    }

    /**
     * Get the permalinks by WooCommerce option
     *
     * @return \stdClass
     */
    private function wooCommercePermalinks(): \stdClass
    {
        return (object)get_option('woocommerce_permalinks', []);
    }

    /**
     * Get the permalinks structure by WordPress option
     *
     * @return string
     */
    private function permalinksStructure(): string
    {
        return (string)get_option('permalink_structure', '');
    }

    /**
     * Retrieve the attribute name by the taxonomy slug
     *
     * @param string $taxonomySlug
     * @return string
     */
    private function attributeNameByTaxonomySlug(string $taxonomySlug): string
    {
        if (false === strpos($taxonomySlug, 'pa_')) {
            return $taxonomySlug;
        }

        return substr($taxonomySlug, 3);
    }
}
