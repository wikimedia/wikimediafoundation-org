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
use Inpsyde\MultilingualPress\Core\PostTypeRepository;

/**
 * Post type settings updater.
 */
class PostTypeSettingsUpdater
{
    const SETTINGS_NAME = 'post_type_settings';
    const SETTINGS_FIELD_ACTIVE = 'active';
    const SETTINGS_FIELD_PERMALINKS = 'permalinks';
    const SETTINGS_FIELD_SKIN = 'ui';

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var PostTypeRepository
     */
    private $repository;

    /**
     * @param PostTypeRepository $repository
     * @param Nonce $nonce
     */
    public function __construct(PostTypeRepository $repository, Nonce $nonce)
    {
        $this->repository = $repository;
        $this->nonce = $nonce;
    }

    /**
     * Updates the post type settings.
     *
     * @param Request $request
     * @return bool
     */
    public function updateSettings(Request $request): bool
    {
        if (!$this->nonce->isValid()) {
            return false;
        }

        $availablePostTypes = $this->repository->allAvailablePostTypes();

        $settings = (array)$request->bodyValue(
            static::SETTINGS_NAME,
            INPUT_POST,
            FILTER_DEFAULT,
            FILTER_FORCE_ARRAY
        );

        if (!$availablePostTypes || !$settings) {
            return $this->repository->removeSupportForAllPostTypes();
        }

        $postTypesSettingData = [];
        $availablePostTypeSlugs = array_keys($availablePostTypes);
        foreach ($availablePostTypeSlugs as $postTypeSlug) {
            $postTypesSettingData[$postTypeSlug] = $this->dataForPostType($postTypeSlug, $settings);
        }

        return $this->repository->supportPostTypes($postTypesSettingData);
    }

    /**
     * @param string $slug
     * @param array $settings
     * @return array
     */
    private function dataForPostType(string $slug, array $settings): array
    {
        if (empty($settings[$slug][self::SETTINGS_FIELD_ACTIVE])) {
            return [
                PostTypeRepository::FIELD_ACTIVE => false,
                PostTypeRepository::FIELD_PERMALINK => false,
            ];
        }

        $hasPermalink = $settings[$slug][self::SETTINGS_FIELD_PERMALINKS] ?? false;

        return [
            PostTypeRepository::FIELD_ACTIVE => true,
            PostTypeRepository::FIELD_PERMALINK => (bool)$hasPermalink,
        ];
    }
}
