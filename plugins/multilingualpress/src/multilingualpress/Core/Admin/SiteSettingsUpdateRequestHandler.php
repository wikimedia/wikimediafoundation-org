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

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use Inpsyde\MultilingualPress\Framework\Setting\SiteSettingsUpdatable;

use function Inpsyde\MultilingualPress\redirectAfterSettingsUpdate;

/**
 * Request handler for site settings update requests.
 */
class SiteSettingsUpdateRequestHandler
{
    const ACTION = 'update_multilingualpress_site_settings';

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var SiteSettingsUpdatable
     */
    private $updater;

    /**
     * @param SiteSettingsUpdatable $updater
     * @param Request $request
     * @param Nonce $nonce
     */
    public function __construct(
        SiteSettingsUpdatable $updater,
        Request $request,
        Nonce $nonce
    ) {

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

        $siteId = (int)$this->request->bodyValue('id', INPUT_REQUEST, FILTER_SANITIZE_NUMBER_INT);
        if (!$siteId) {
            wp_die('Invalid site', 'Invalid site', 403);
        }

        $this->updater->updateSettings($siteId);

        redirectAfterSettingsUpdate();
    }
}
