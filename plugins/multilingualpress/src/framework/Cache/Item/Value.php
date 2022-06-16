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

namespace Inpsyde\MultilingualPress\Framework\Cache\Item;

final class Value
{

    /**
     * @var bool
     */
    private $hit;

    /**
     * @var mixed|null
     */
    private $value;

    /**
     * @param null $value
     * @param bool $hit
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration
     */
    public function __construct($value = null, bool $hit = false)
    {
        // phpcs:enable

        $this->value = $value;
        $this->hit = $hit;
    }

    /**
     * @return bool
     */
    public function isHit(): bool
    {
        return $this->hit;
    }

    /**
     * @return mixed|null
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
     */
    public function value()
    {
        // phpcs:enable

        return $this->value;
    }
}
