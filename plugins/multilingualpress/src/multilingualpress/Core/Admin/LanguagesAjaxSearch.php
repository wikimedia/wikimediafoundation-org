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

use Inpsyde\MultilingualPress\Database\Table\LanguagesTable;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Language\Language;

use function Inpsyde\MultilingualPress\allLanguages;

class LanguagesAjaxSearch
{
    const ACTION = 'multilingualpress_search_languages';
    const SEARCH_PARAM = 'search';

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return void
     */
    public function handle()
    {
        if (!wp_doing_ajax()) {
            return;
        }

        if (!doing_action('wp_ajax_' . self::ACTION)) {
            wp_send_json_error('Invalid action.');
        }

        $search = $this->request->bodyValue(self::SEARCH_PARAM, INPUT_POST, FILTER_SANITIZE_STRING);
        if (!$search) {
            wp_send_json_success([]);
        }

        $languages = allLanguages();

        $found = [];
        foreach ($languages as $language) {
            $item = $this->foundItem($search, $language);
            $item and $found[] = $item;
        }

        if (!$found) {
            wp_send_json_success([]);
        }

        uasort(
            $found,
            static function (array $left, array $right): int {
                return strcasecmp($left['label'], $right['label']);
            }
        );

        wp_send_json_success(array_values($found));
    }

    /**
     * @param string $search
     * @param Language $language
     * @return array
     */
    private function foundItem(string $search, Language $language): array
    {
        $englishName = $language->englishName();
        $names = [$englishName];
        $englishMatch = preg_match('~ \(([^)]+)\)~', $englishName, $englishMatches);
        $englishMatch and $names[] = $englishMatches[1];

        $nativeName = $language->nativeName();
        if ($nativeName) {
            $names[] = $nativeName;
            $nativeMatch = preg_match('~ \(([^)]+)\)~', $nativeName, $nativeMatches);
            $nativeMatch and $names[] = $nativeMatches[1];
        }

        $names[] = $language->bcp47tag();
        $names[] = $language->locale();
        $names[] = $language->isoCode(LanguagesTable::COLUMN_ISO_639_3_CODE);
        $names[] = $language->isoCode(LanguagesTable::COLUMN_ISO_639_3_CODE);

        $found = false;
        while (!$found && $names) {
            $name = array_shift($names);
            $found = $name ? stripos($name, $search) === 0 : false;
        }

        if (!$found) {
            return [];
        }

        return [
            'label' => $language->name(),
            'value' => $language->bcp47tag(),
            'language' => [
                'nativeLanguage' => $language->name(),
                'httpCode' => $language->bcp47tag(),
                'englishName' => $language->englishName(),
                'nativeName' => $language->nativeName(),
                'iso639Code1' => $language->isoCode(LanguagesTable::COLUMN_ISO_639_1_CODE),
                'iso639Code2' => $language->isoCode(LanguagesTable::COLUMN_ISO_639_2_CODE),
                'iso639Code3' => $language->isoCode(LanguagesTable::COLUMN_ISO_639_3_CODE),
                'isoName' => $language->isoName(),
                'locale' => $language->locale(),
                'isRtl' => $language->isRtl(),
            ],
        ];
    }
}
