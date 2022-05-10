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

namespace Inpsyde\MultilingualPress\Module\LanguageManager;

use Inpsyde\MultilingualPress\Database\Table\LanguagesTable;
use Inpsyde\MultilingualPress\Framework\Language\Language;

/**
 * Class LanguageInstaller
 */
class LanguageInstaller
{
    /**
     * @param Language $language
     * @return bool
     */
    public function install(Language $language): bool
    {
        $this->loadFunctions();

        if ($this->exists($language)) {
            return false;
        }

        $languageCode = $this->codeLanguageFromWpOrg($language);
        if (!$languageCode) {
            return false;
        }

        return (bool)wp_download_language_pack($languageCode);
    }

    /**
     * @param Language $language
     * @return bool
     */
    public function exists(Language $language): bool
    {
        $this->loadFunctions();

        $exists = false;
        $contexts = wp_get_installed_translations('core');

        foreach ($contexts as $context) {
            if (array_intersect($this->languageIdentifiers($language), array_keys($context))) {
                $exists = true;
                break;
            }
        }

        return $exists;
    }

    /**
     * @param Language $language
     * @return string
     */
    private function codeLanguageFromWpOrg(Language $language): string
    {
        $availableLanguages = wp_get_available_translations();

        if (!wp_can_install_language_pack()) {
            return '';
        }

        $languageIdentifiers = $this->languageIdentifiers($language);
        $availableLanguagesKeys = array_keys($availableLanguages);

        if (in_array($languageIdentifiers[0], $availableLanguagesKeys, true)) {
            return $languageIdentifiers[0];
        }

        foreach ($availableLanguages as $code => $data) {
            $identifier = false;
            if (array_intersect($languageIdentifiers, $data['iso'])) {
                $identifier = $code;
            }

            if ($identifier) {
                return $identifier;
            }
        }

        return '';
    }

    /**
     * @param Language $language
     * @return array
     */
    private function languageIdentifiers(Language $language): array
    {
        return array_filter(array_unique([
            $language->locale(),
            $language->isoCode(LanguagesTable::COLUMN_ISO_639_1_CODE),
            $language->isoCode(LanguagesTable::COLUMN_ISO_639_2_CODE),
            $language->isoCode(LanguagesTable::COLUMN_ISO_639_3_CODE),
        ]));
    }

    /**
     * Load missed functions.
     */
    private function loadFunctions()
    {
        if (!function_exists('wp_get_available_translations')) {
            require_once(ABSPATH . 'wp-admin/includes/translation-install.php');
        }
    }
}
