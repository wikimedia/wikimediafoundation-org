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

namespace Inpsyde\MultilingualPress\Module\Trasher;

use Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\NetworkState;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;

/**
 * Trasher setting updater.
 */
class TrasherSettingUpdater
{

    /**
     * @var ActivePostTypes
     */
    private $activePostTypes;

    /**
     * @var ContentRelations
     */
    private $contentRelations;

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var TrasherSettingRepository
     */
    private $settingRepository;

    /**
     * @param TrasherSettingRepository $settingRepository
     * @param ContentRelations $contentRelations
     * @param Request $request
     * @param Nonce $nonce
     * @param ActivePostTypes $activePostTypes
     */
    public function __construct(
        TrasherSettingRepository $settingRepository,
        ContentRelations $contentRelations,
        Request $request,
        Nonce $nonce,
        ActivePostTypes $activePostTypes
    ) {

        $this->settingRepository = $settingRepository;
        $this->contentRelations = $contentRelations;
        $this->request = $request;
        $this->nonce = $nonce;
        $this->activePostTypes = $activePostTypes;
    }

    /**
     * Updates the trasher setting of the post with the given ID as well as all related posts.
     *
     * @param int $postId
     * @param \WP_Post $post
     * @return int
     */
    public function update(int $postId, \WP_Post $post): int
    {
        if (!$this->activePostTypes->arePostTypesActive((string)$post->post_type)) {
            return 0;
        }

        if (!$this->nonce->isValid()) {
            return 0;
        }

        if (!in_array($post->post_status, ['publish', 'draft'], true)) {
            return 0;
        }

        $value = (bool)$this->request->bodyValue(
            TrasherSettingRepository::META_KEY,
            INPUT_POST,
            FILTER_VALIDATE_BOOLEAN
        );

        return $this->updateSetting($postId, $value);
    }

    /**
     * @param \WP_Post $post
     * @param \WP_REST_Request $request
     * @return int
     */
    public function updateFromRestApi(\WP_Post $post, \WP_REST_Request $request): int
    {
        if (!$this->activePostTypes->arePostTypesActive((string)$post->post_type)) {
            return 0;
        }

        if (!in_array($post->post_status, ['publish', 'draft'], true)) {
            return 0;
        }

        $value = (bool)sanitize_key($request->get_param('_trash_the_other_posts'));
        $postId = (int)$post->ID;

        return $this->updateSetting($postId, $value);
    }

    /**
     * @param int $postId
     * @param bool $value
     * @return int
     */
    private function updateSetting(int $postId, bool $value): int
    {
        if (!$this->settingRepository->updateSetting($postId, $value)) {
            return 0;
        }

        $currentSiteId = get_current_blog_id();
        $relatedPosts = $this->contentRelations->relations($currentSiteId, $postId, 'post');

        unset($relatedPosts[$currentSiteId]);

        if (!$relatedPosts) {
            return 1;
        }

        $updatedPosts = 1;

        $networkState = NetworkState::create();
        foreach ($relatedPosts as $siteId => $relPostId) {
            switch_to_blog($siteId);
            $this->settingRepository->updateSetting((int)$relPostId, $value) and $updatedPosts++;
        }
        $networkState->restore();

        return $updatedPosts;
    }
}
