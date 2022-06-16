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

namespace Inpsyde\MultilingualPress\Module\QuickLinks;

use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

use function Inpsyde\MultilingualPress\callExit;

/**
 * Class Redirector
 * @package Inpsyde\MultilingualPress\Module\QuickLinks
 */
class Redirector
{
    const REDIRECT_VALUE_KEY = 'mlp_quicklinks_redirect_selection';
    const ACTION_BEFORE_VALIDATE_REDIRECT = 'multilingualpress.before_validate_redirect';
    const ACTION_AFTER_VALIDATE_REDIRECT = 'multilingualpress.after_validate_redirect';

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * Redirector constructor.
     * @param Nonce $nonce
     */
    public function __construct(Nonce $nonce)
    {
        $this->nonce = $nonce;
    }

    /**
     * Take Redirect Action
     *
     * @return void
     */
    public function redirect()
    {
        if (!$this->nonce->isValid()) {
            return;
        }

        $url = (string)filter_input(INPUT_POST, self::REDIRECT_VALUE_KEY, FILTER_SANITIZE_URL);

        /**
         * Action Before the Redirect Url get Validate
         */
        do_action(self::ACTION_BEFORE_VALIDATE_REDIRECT);

        $url and $url = wp_validate_redirect($url);

        /**
         * Action After the Redirect Url has been Validated
         */
        do_action(self::ACTION_AFTER_VALIDATE_REDIRECT);

        if (!$url) {
            return;
        }

        //phpcs:disable WordPressVIPMinimum.Security.ExitAfterRedirect.NoExit
        wp_safe_redirect($url, 303);
        callExit();
    }
}
