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

namespace Inpsyde\MultilingualPress\TranslationUi\Post\Ajax;

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

use function Inpsyde\MultilingualPress\siteExists;

class ContextBuilder
{
    const SOURCE_SITE_PARAM = 'source_site_id';
    const SOURCE_POST_PARAM = 'source_post_id';
    const REMOTE_SITE_PARAM = 'remote_site_id';
    const REMOTE_POST_PARAM = 'remote_post_id';
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
     * @return RelationshipContext
     */
    public function build(): RelationshipContext
    {
        if (!wp_doing_ajax()) {
            return new RelationshipContext();
        }

        $sourceSiteId = (int)$this->request->bodyValue(
            self::SOURCE_SITE_PARAM,
            INPUT_POST,
            FILTER_SANITIZE_NUMBER_INT
        );
        $sourcePostId = (int) $this->request->bodyValue(
            self::SOURCE_POST_PARAM,
            INPUT_POST,
            FILTER_SANITIZE_NUMBER_INT
        );
        $remoteSiteId = (int)$this->request->bodyValue(
            self::REMOTE_SITE_PARAM,
            INPUT_POST,
            FILTER_SANITIZE_NUMBER_INT
        );
        $remotePostId = (int) $this->request->bodyValue(
            self::REMOTE_POST_PARAM,
            INPUT_POST,
            FILTER_SANITIZE_NUMBER_INT
        );

        if (
            !$sourceSiteId
            || !$sourcePostId
            || !$remoteSiteId
            || !siteExists($sourceSiteId)
            || !siteExists($remoteSiteId)
        ) {
            wp_send_json_error('Invalid context.');
        }

        return new RelationshipContext(
            [
                RelationshipContext::REMOTE_POST_ID => $remotePostId,
                RelationshipContext::REMOTE_SITE_ID => $remoteSiteId,
                RelationshipContext::SOURCE_POST_ID => $sourcePostId,
                RelationshipContext::SOURCE_SITE_ID => $sourceSiteId,
            ]
        );
    }
}
