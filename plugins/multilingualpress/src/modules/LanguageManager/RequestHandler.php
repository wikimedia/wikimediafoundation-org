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

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

use function Inpsyde\MultilingualPress\redirectAfterSettingsUpdate;

class RequestHandler
{
    const ACTION = 'update_multilingualpress_languages';

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Updater
     */
    private $updater;

    /**
     * @param Updater $updater
     * @param Request $request
     * @param Nonce $nonce
     */
    public function __construct(Updater $updater, Request $request, Nonce $nonce)
    {
        $this->updater = $updater;
        $this->request = $request;
        $this->nonce = $nonce;
    }

    /**
     * Handles POST requests.
     */
    public function handlePostRequest()
    {
        if (!$this->nonce->isValid()) {
            wp_die('Invalid', 'Invalid', 403);
        }

        $languages = $this->request->bodyValue('languages', INPUT_POST);
        if (!$languages) {
            return;
        }

        $this->ensureLanguagesData($languages);

        $this->updater->updateLanguages($languages);

        redirectAfterSettingsUpdate();
    }

    /**
     * @param array $languages
     *
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     */
    private function ensureLanguagesData(array &$languages)
    {
        // phpcs:enable

        array_walk($languages, static function (array &$language) {
            if (!array_key_exists('is_rtl', $language)) {
                $language['is_rtl'] = 0;
            }
        });
    }
}
