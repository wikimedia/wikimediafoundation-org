<?php # -*- coding: utf-8 -*-
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
use Inpsyde\MultilingualPress\Framework\Setting\SiteSettingsUpdatable;

/**
 * Site settings updater.
 */
class SiteSettingsUpdater implements SiteSettingsUpdatable
{
    const ACTION_DEFINE_INITIAL_SETTINGS = 'multilingualpress.define_initial_site_settings';
    const ACTION_UPDATE_SETTINGS = 'multilingualpress.update_site_settings';
    const VALUE_LANGUAGE_NONE = '-1';

    /**
     * @var SiteSettingsRepository
     */
    private $repository;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param SiteSettingsRepository $repository
     * @param Request $request
     */
    public function __construct(SiteSettingsRepository $repository, Request $request)
    {
        $this->repository = $repository;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function defineInitialSettings(int $siteId)
    {
        if ($siteId < 1) {
            return;
        }

        $this->updateWpLang($siteId);
        $this->updateLanguage($siteId);
        $this->updateRelationships($siteId);
        $this->updateXDefault($siteId);

        /**
         * Fires right after the initial settings of a new site have been defined.
         *
         * @param int $siteId
         */
        do_action(self::ACTION_DEFINE_INITIAL_SETTINGS, $siteId);
    }

    /**
     * @inheritdoc
     */
    public function updateSettings(int $siteId)
    {
        $this->updateLanguage($siteId);
        $this->updateRelationships($siteId);
        $this->updateXDefault($siteId);

        /**
         * Fires right after the initial settings of an existing site have been updated.
         *
         * @param int $siteId
         */
        do_action(self::ACTION_UPDATE_SETTINGS, $siteId);
    }

    /**
     * Returns the language value from the request.
     *
     * @return string
     */
    private function targetLanguage(): string
    {
        $language = $this->request->bodyValue(
            SiteSettingsRepository::NAME_LANGUAGE,
            INPUT_POST,
            FILTER_SANITIZE_STRING
        );

        if (!is_string($language) || self::VALUE_LANGUAGE_NONE === $language) {
            $language = '';
        }

        return $language;
    }

    /**
     * Updates the language for the site with the given ID according to request.
     *
     * @param int $siteId
     */
    private function updateLanguage(int $siteId)
    {
        $this->repository->updateLanguage($this->targetLanguage(), $siteId);
    }

    /**
     * Updates the relationships for the site with the given ID according to request.
     *
     * @param int $siteId
     */
    private function updateRelationships(int $siteId)
    {
        $relationships = (array)$this->request->bodyValue(
            SiteSettingsRepository::NAME_RELATIONSHIPS,
            INPUT_POST,
            FILTER_SANITIZE_NUMBER_INT,
            FILTER_FORCE_ARRAY
        );

        $this->repository->relate(array_map('intval', $relationships), $siteId);
    }

    /**
     * Updates xDefault value.
     * @param int $siteId
     */
    private function updateXDefault(int $siteId)
    {
        // May not exists.
        $xDefault = (int)$this->request->bodyValue(
            SiteSettingsRepository::NAME_XDEFAULT,
            INPUT_POST,
            FILTER_SANITIZE_NUMBER_INT
        );

        $this->repository->updateXDefault($xDefault, $siteId);
    }

    /**
     * Updates the WordPress language for the site with the given ID according to request.
     *
     * @param int $siteId
     */
    private function updateWpLang(int $siteId)
    {
        $wplang = (string)$this->request->bodyValue(
            'WPLANG',
            INPUT_POST,
            FILTER_SANITIZE_STRING
        );

        if (in_array($wplang, get_available_languages(), true)) {
            update_blog_option($siteId, 'WPLANG', $wplang);
        }
    }
}
