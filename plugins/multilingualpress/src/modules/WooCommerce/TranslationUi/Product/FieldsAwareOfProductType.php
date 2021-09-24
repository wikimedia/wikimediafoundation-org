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

namespace Inpsyde\MultilingualPress\Module\WooCommerce\TranslationUi\Product;

/**
 * Class FieldsAwareOfProductType
 */
class FieldsAwareOfProductType
{
    const OPTIONS = [
        MetaboxFields::FIELD_OVERRIDE_VARIATIONS,
        MetaboxFields::FIELD_GROUPED_PRODUCTS,
        MetaboxFields::FIELD_PRODUCT_URL,
        MetaboxFields::FIELD_PRODUCT_URL_BUTTON_TEXT,
    ];

    /**
     * Check if the same product type is needed based on the give values and the options
     *
     * @param array $values
     * @return bool
     */
    public static function needSameProductType(array $values): bool
    {
        $requestValueKeys = array_keys($values);
        $exists = array_intersect(self::OPTIONS, $requestValueKeys);

        if (!$exists) {
            return false;
        }

        foreach ($values as $key => $value) {
            if (\in_array($key, self::OPTIONS, true) && $value) {
                return true;
            }
        }

        return false;
    }
}
