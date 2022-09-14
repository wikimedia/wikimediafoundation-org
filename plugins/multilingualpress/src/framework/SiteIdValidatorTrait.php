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

namespace Inpsyde\MultilingualPress\Framework;

use UnexpectedValueException;

/**
 * Trait SiteIdValidatorTrait
 * @package Inpsyde\MultilingualPress\Framework
 */
trait SiteIdValidatorTrait
{
    /**
     * @param int $siteId
     * @throws UnexpectedValueException
     */
    protected function siteIdMustBeGreaterThanZero(int $siteId)
    {
        if ($siteId <= 0) {
            throw new UnexpectedValueException(
                "Site Id cannot be less than One. {$siteId} value given."
            );
        }
    }
}
