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

/**
 * Trait SwitchSiteHelper
 * @package Inpsyde\MultilingualPress\Framework
 */
trait SwitchSiteTrait
{
    /**
     * @param int $targetSiteId
     * @return int
     */
    protected function maybeSwitchSite(int $targetSiteId): int
    {
        $currentSite = get_current_blog_id();
        if ($currentSite !== $targetSiteId) {
            switch_to_blog($targetSiteId);

            return $currentSite;
        }

        return -1;
    }

    /**
     * @param int $originalSiteId
     * @return bool
     */
    protected function maybeRestoreSite(int $originalSiteId): bool
    {
        if ($originalSiteId < 0) {
            return false;
        }

        restore_current_blog();

        $currentSite = get_current_blog_id();
        if ($currentSite !== $originalSiteId) {
            switch_to_blog($originalSiteId);
        }

        return true;
    }
}
