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
use Inpsyde\MultilingualPress\Core\TaxonomyRepository;

/**
 * Taxonomy settings updater.
 */
class TaxonomySettingsUpdater
{
    const SETTINGS_NAME = 'taxonomy_settings';
    const SETTINGS_FIELD_ACTIVE = 'active';
    const SETTINGS_FIELD_SKIN = 'ui';

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @var TaxonomyRepository
     */
    private $repository;

    /**
     * @param TaxonomyRepository $repository
     * @param Nonce $nonce
     */
    public function __construct(TaxonomyRepository $repository, Nonce $nonce)
    {
        $this->repository = $repository;
        $this->nonce = $nonce;
    }

    /**
     * Updates the taxonomy settings.
     *
     * @param Request $request
     * @return bool
     */
    public function updateSettings(Request $request): bool
    {
        if (!$this->nonce->isValid()) {
            return false;
        }

        $availableTaxonomies = $this->repository->allAvailableTaxonomies();

        $settings = (array)$request->bodyValue(
            static::SETTINGS_NAME,
            INPUT_POST,
            FILTER_DEFAULT,
            FILTER_FORCE_ARRAY
        );

        if (!$availableTaxonomies || !$settings) {
            return $this->repository->removeSupportForAllTaxonomies();
        }

        $taxonomySettingData = [];
        $availableTaxonomySlugs = array_keys($availableTaxonomies);
        foreach ($availableTaxonomySlugs as $taxonomySlug) {
            $taxonomySettingData[$taxonomySlug] = $this->dataForTaxonomy($taxonomySlug, $settings);
        }

        return $this->repository->supportTaxonomies($taxonomySettingData);
    }

    /**
     * @param string $slug
     * @param array $settings
     * @return array
     */
    private function dataForTaxonomy(string $slug, array $settings): array
    {
        if (empty($settings[$slug][self::SETTINGS_FIELD_ACTIVE])) {
            return [
                TaxonomyRepository::FIELD_ACTIVE => false,
                TaxonomyRepository::FIELD_SKIN => '',
            ];
        }

        $taxonomyUi = $settings[$slug][self::SETTINGS_FIELD_SKIN] ?? '';

        return [
            TaxonomyRepository::FIELD_ACTIVE => true,
            TaxonomyRepository::FIELD_SKIN => (string)$taxonomyUi,
        ];
    }
}
