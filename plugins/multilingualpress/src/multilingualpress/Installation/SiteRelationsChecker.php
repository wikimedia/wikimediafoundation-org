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

namespace Inpsyde\MultilingualPress\Installation;

use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;

class SiteRelationsChecker
{
    /**
     * @var SiteRelations
     */
    private $siteRelations;

    /**
     * @param SiteRelations $siteRelations
     */
    public function __construct(SiteRelations $siteRelations)
    {
        $this->siteRelations = $siteRelations;
    }

    /**
     * Checks if there are at least two sites related to each other, and renders an admin notice if not.
     *
     * @return bool
     */
    public function checkRelations(): bool
    {
        $success = false;

        if (
            wp_doing_ajax()
            || is_network_admin()
            || !is_super_admin()
            || $this->siteRelations->allRelations()
        ) {
            $success = true;
        }

        return $success;
    }
}
