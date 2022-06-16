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

namespace Inpsyde\MultilingualPress\Framework\Setting\Site;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

class SiteSettingUpdater
{

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var string
     */
    private $option;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param string $option
     * @param Request $request
     * @param Nonce|null $nonce
     */
    public function __construct(
        string $option,
        Request $request,
        Nonce $nonce = null
    ) {

        $this->option = $option;
        $this->request = $request;
        $this->nonce = $nonce;
    }

    /**
     * Updates the setting with the given data for the site with the given ID.
     *
     * @param int $siteId
     * @return bool
     */
    public function update(int $siteId): bool
    {
        if (!current_user_can('manage_sites')) {
            return false;
        }

        if ($this->nonce && !$this->nonce->isValid()) {
            return false;
        }

        $value = $this->request->bodyValue(
            $this->option,
            INPUT_REQUEST,
            FILTER_SANITIZE_STRING
        );

        return $value
            ? update_blog_option($siteId, $this->option, $value)
            : delete_blog_option($siteId, $this->option);
    }
}
