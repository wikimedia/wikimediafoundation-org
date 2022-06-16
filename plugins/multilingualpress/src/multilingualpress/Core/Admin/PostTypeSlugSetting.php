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

use Inpsyde\MultilingualPress\Core\PostTypeRepository;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel;

class PostTypeSlugSetting implements SiteSettingViewModel
{
    /**
     * @var string
     */
    private $id = 'mlp-post-type-slugs';

    /**
     * @var SiteSettingsRepository
     */
    private $repository;

    /**
     * @var PostTypeRepository
     */
    private $postTypeRepository;

    /**
     * @var \WP_Post_Type
     */
    private $postType;

    /**
     * @param SiteSettingsRepository $repository
     */
    public function __construct(
        PostTypeSlugsSettingsRepository $repository,
        PostTypeRepository $postTypeRepository,
        \WP_Post_Type $postType
    ) {

        $this->repository = $repository;
        $this->postTypeRepository = $postTypeRepository;
        $this->postType = $postType;
    }

    /**
     * @inheritdoc
     */
    public function render(int $siteId)
    {
        $isActive = $this->postTypeRepository->isPostTypeActive($this->postType->name);
        $slugs = $this->repository->postTypeSlugs($siteId);
        ?>
        <input
            type="text"
            name="<?= esc_attr($this->fieldName($this->postType->name)) ?>"
            value="<?= esc_attr($slugs[$this->postType->name] ?? '') ?>"
            class="regular-text"
            id="<?= esc_attr($this->id) ?>"
            <?= (!$isActive ? 'disabled="disabled"' : '') ?>
        />
        <?php
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return sprintf(
            '<label for="%2$s">%1$s</label>',
            esc_html($this->postType->label),
            esc_attr($this->id)
        );
    }

    /**
     * @param string $postType
     * @return string
     */
    private function fieldName(string $postType): string
    {
        return PostTypeSlugsSettingsRepository::POST_TYPE_SLUGS . "[{$postType}]";
    }
}
