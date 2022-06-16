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

namespace Inpsyde\MultilingualPress\TranslationUi;

use Inpsyde\MultilingualPress\Framework\Http\Request;

class MetaboxFieldsHelper
{
    const NAME_PREFIX = 'multilingualpress';
    const ID_PREFIX = 'multilingualpress-';
    /**
     * @var int
     */
    private $siteId;

    /**
     * @param int $siteId
     */
    public function __construct(int $siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * @param string $fieldKey
     * @return string
     */
    public function fieldId(string $fieldKey): string
    {
        return self::ID_PREFIX . "site-{$this->siteId}-{$fieldKey}";
    }

    /**
     * @param string $fieldKey
     * @return string
     */
    public function fieldName(string $fieldKey): string
    {
        return self::NAME_PREFIX . "[site-{$this->siteId}][{$fieldKey}]";
    }

    /**
     * @param Request $request
     * @param string $fieldKey
     * @param null $default
     * @return mixed
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
     */
    public function fieldRequestValue(Request $request, string $fieldKey, $default = null)
    {
        // phpcs:enable

        $allValues = $request->bodyValue(
            self::NAME_PREFIX,
            INPUT_POST,
            FILTER_UNSAFE_RAW,
            FILTER_FORCE_ARRAY
        );

        if (!is_array($allValues) || !$allValues) {
            return $default;
        }

        return $allValues["site-{$this->siteId}"][$fieldKey] ?? $default;
    }
}
