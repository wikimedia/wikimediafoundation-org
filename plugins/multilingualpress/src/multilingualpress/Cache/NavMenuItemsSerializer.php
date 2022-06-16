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

namespace Inpsyde\MultilingualPress\Cache;

/**
 * Class NavMenuItemSerializer
 * @package Inpsyde\MultilingualPress\Cache
 */
/**
 * Class NavMenuItemSerializer
 * @package Inpsyde\MultilingualPress\Cache
 */
class NavMenuItemsSerializer
{
    const ALLOWED_MENU_ITEM_FILTER = 'mlp.cache.allowed-nav-menu-items';

    /**
     * @var string[]
     */
    const POST_ALLOWED_PROPERTIES = [
        'ID',
        'filter',
    ];

    /**
     * @var string []
     */
    const MENU_ITEM_ALLOWED_PROPERTIES = [
        ['menu_item_parent', 'int', 0],
        ['db_id', 'int', 0],
        ['object_id', 'int', 0],
        ['object', 'string', ''],
        ['type', 'string', ''],
        ['type_label', 'string', ''],
        ['title', 'string', ''],
        ['url', 'string', ''],
        ['target', 'string', ''],
        ['attr_title', 'string', ''],
        ['description', 'string', ''],
        ['classes', 'array', ''],
        ['xfn', 'string', ''],
        ['current', 'bool', false],
        ['current_item_ancestor', 'bool', false],
        ['current_item_parent', 'bool', false],
    ];

    /**
     * @var \WP_Post[]
     */
    private $unserialized;

    /**
     * @var array[]
     */
    private $serialized;

    /**
     * @param \WP_Post[] $items
     * @return NavMenuItemsSerializer
     */
    public static function fromWpPostItems(\WP_Post ...$items): NavMenuItemsSerializer
    {
        return new static($items);
    }

    /**
     * @param array[] $items
     * @return NavMenuItemsSerializer
     */
    public static function fromSerialized(array ...$items): NavMenuItemsSerializer
    {
        return new static(null, $items);
    }

    /**
     * @param array|null $unserialized
     * @param array|null $serialized
     */
    private function __construct(array $unserialized = null, array $serialized = null)
    {
        $this->unserialized = $unserialized;
        $this->serialized = $serialized;
    }

    /**
     * @return \WP_Post[]
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    public function unserialize(): array
    {
        if (is_array($this->unserialized)) {
            return $this->unserialized;
        }

        $this->unserialized = [];
        foreach ($this->serialized as $postArray) {
            $post = get_post(
                $postArray['post']['ID'] ?? 0,
                OBJECT,
                $postArray['post']['filter'] ?? 'raw'
            );

            if (!$post) {
                continue;
            }

            $menuItem = $postArray['menu_item'] ?? [];
            foreach (self::filterAllowedProperties() as list($property, $type, $default)) {
                if (!isset($post->{$property})) {
                    $post->{$property} = $this->extractValue($menuItem, $property, $type, $default);
                }
            }

            $this->unserialized[] = $post;
        }

        return $this->unserialized;
    }

    /**
     * @return array[]
     */
    public function serialize(): array
    {
        if (is_array($this->serialized)) {
            return $this->serialized;
        }

        $this->serialized = [];
        foreach ($this->unserialized as $post) {
            $key = md5($post->ID . $post->post_modified_gmt);
            $this->serialized[$key] = $this->splitPostProperties($post);
        }

        return $this->serialized;
    }

    /**
     * @param \WP_Post $post
     * @return array
     */
    private function splitPostProperties(\WP_Post $post): array
    {
        $postsArray = [];
        $properties = $this->menuItemProperties();

        foreach ($post->to_array() as $property => $value) {
            if (in_array($property, self::POST_ALLOWED_PROPERTIES, true)) {
                $postsArray['post'][$property] = $value;
            }

            if (in_array($property, $properties, true)) {
                $postsArray['menu_item'][$property] = $value;
            }
        }

        return $postsArray;
    }

    /**
     * @param array $array
     * @param string $key
     * @param string $type
     * @param $default
     * @return mixed
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    private function extractValue(array $array, string $key, string $type, $default)
    {
        $value = $array[$key] ?? $default;

        switch ($type) {
            case 'int':
                $value = (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                break;
            case 'float':
                $value = (float)filter_var(
                    $value,
                    FILTER_SANITIZE_NUMBER_FLOAT,
                    FILTER_FLAG_ALLOW_FRACTION
                );
                break;
            case 'string':
                $value = (string)$value;
                break;
            case 'bool':
                $value = (bool)filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;
            case 'array':
                $value = (array)$value;
                break;
        }

        return $value;
    }

    /**
     * @return array
     */
    private function menuItemProperties(): array
    {
        static $properties = [];

        if (!$properties) {
            $properties = array_map(static function (array $item): string {
                return $item[0];
            }, self::filterAllowedProperties());
        }

        return $properties;
    }

    private static function filterAllowedProperties(): array
    {
        $allowedProperties = self::MENU_ITEM_ALLOWED_PROPERTIES;
        $additionalProperties = \array_filter((array)\apply_filters(self::ALLOWED_MENU_ITEM_FILTER, []));

        return \array_merge($additionalProperties, $allowedProperties);
    }
}
