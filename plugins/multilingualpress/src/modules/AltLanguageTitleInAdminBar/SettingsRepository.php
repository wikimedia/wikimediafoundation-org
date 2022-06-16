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

namespace Inpsyde\MultilingualPress\Module\AltLanguageTitleInAdminBar;

class SettingsRepository
{
    const OPTION_SITE = 'multilingualpress_alt_language_title';

    /**
     * Returns the alternative language title of the site with the given ID.
     *
     * @param int $siteId
     * @return string
     */
    public function alternativeLanguageTitle(int $siteId): string
    {
        $title = (string)get_blog_option(
            $siteId ?: get_current_blog_id(),
            self::OPTION_SITE
        );

        return stripslashes($title);
    }
}
