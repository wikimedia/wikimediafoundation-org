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

use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\Cache\Server\Facade;

class SiteSettingsRepository
{
    use SiteSettingsRepositoryTrait;

    const KEY_LANGUAGE = 'lang';
    const NAME_LANGUAGE = 'mlp_site_language';
    const NAME_LANGUAGE_TYPE = 'mlp_site_language_type';
    const NAME_RELATIONSHIPS = 'mlp_site_relations';
    const NAME_XDEFAULT = 'multilingualpress_xdefault';
    const OPTION = 'multilingualpress_site_settings';

    /**
     * @var SiteRelations
     */
    private $siteRelations;

    /**
     * @var Facade
     */
    private $cache;

    /**
     * @param SiteRelations $siteRelations
     * @param Facade $cache
     */
    public function __construct(SiteRelations $siteRelations, Facade $cache)
    {
        $this->siteRelations = $siteRelations;
        $this->cache = $cache;
    }

    /**
     * Returns an array with the IDs of all sites with an assigned language,
     * minus the given IDs, if any.
     *
     * @param int[] $exclude
     * @return int[]
     */
    public function allSiteIds(array $exclude = []): array
    {
        $settings = $this->allSettings();
        if (!$settings) {
            return [];
        }

        $ids = wp_parse_id_list(array_keys($settings));

        return $exclude ? array_diff($ids, wp_parse_id_list($exclude)) : $ids;
    }

    /**
     * Returns the site language of the site with the given ID, or the current site.
     *
     * @param int|null $siteId
     * @return string
     */
    public function siteLanguageTag(int $siteId = null): string
    {
        $siteId = $siteId ?: get_current_blog_id();

        $settings = $this->allSettings();
        $language = $settings[$siteId][self::KEY_LANGUAGE] ?? '';

        return stripslashes($language);
    }

    /**
     * Sets the language for the site with the given ID, or the current site.
     *
     * @param string $language
     * @param int|null $siteId
     * @return bool
     */
    public function updateLanguage(string $language, int $siteId = null): bool
    {
        return $this->updateSetting(
            self::KEY_LANGUAGE,
            $language,
            $siteId
        );
    }

    /**
     * Sets the relationships for the site with the given ID, or the current site.
     *
     * @param int[]
     * @param int|null $baseSiteId
     * @return bool
     */
    public function relate(array $siteIds, int $baseSiteId = null): bool
    {
        return (bool)$this->siteRelations->relateSites(
            $baseSiteId ?: get_current_blog_id(),
            $siteIds
        );
    }

    /**
     * Updates xDefault setting value.
     * @param int $xDefault
     * @param int|null $siteId
     * @return bool
     */
    public function updateXDefault(int $xDefault, int $siteId = null): bool
    {
        return $this->updateSetting(
            self::NAME_XDEFAULT,
            $xDefault,
            $siteId
        );
    }
}
