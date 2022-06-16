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

namespace Inpsyde\MultilingualPress\Core\Admin;

/**
 * Class LanguageInstaller
 */
class LanguageInstaller
{
    /**
     * @param string $languageCode The WordPress language code
     */
    public function install(string $languageCode)
    {
        if (!wp_can_install_language_pack() || empty($languageCode) || !current_user_can('install_languages')) {
            return;
        }

        wp_download_language_pack($languageCode);
        update_site_option('WPLANG', $languageCode);
    }
}
