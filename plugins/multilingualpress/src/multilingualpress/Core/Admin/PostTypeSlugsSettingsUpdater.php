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

/**
 * Class PostTypeSlugsSettingsUpdater
 */
class PostTypeSlugsSettingsUpdater
{
    /**
     * @var SiteSettingsRepository
     */
    private $repository;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param PostTypeSlugsSettingsRepository $repository
     * @param Request $request
     */
    public function __construct(PostTypeSlugsSettingsRepository $repository, Request $request)
    {
        $this->repository = $repository;
        $this->request = $request;
    }

    /**
     * @param int $siteId
     */
    public function updateSettings(int $siteId)
    {
        $this->updatePostTypeSlugs($siteId);
    }

    /**
     * Update the Translation of Post Type Slugs for the site with the given ID according to request.
     *
     * @param int $siteId
     */
    private function updatePostTypeSlugs(int $siteId)
    {
        $slugs = array_filter((array)$this->request->bodyValue(
            PostTypeSlugsSettingsRepository::POST_TYPE_SLUGS,
            INPUT_POST
        ));

        foreach ($slugs as &$slug) {
            $slug = sanitize_text_field($slug);
        }

        $this->repository->updatePostTypeSlugs($slugs, $siteId);
    }
}
