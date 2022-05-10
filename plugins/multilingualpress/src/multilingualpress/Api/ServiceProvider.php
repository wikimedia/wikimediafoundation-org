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

namespace Inpsyde\MultilingualPress\Api;

use Inpsyde\MultilingualPress\Core\Admin\Settings\Cache\CacheSettingsRepository;
use Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes;
use Inpsyde\MultilingualPress\Core\Entity\ActiveTaxonomies;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Api\Languages;
use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\Api\Translations as FrameworkTranslations;
use Inpsyde\MultilingualPress\Framework\Api\TranslationSearchArgs;
use Inpsyde\MultilingualPress\Framework\Cache\Server\Facade;
use Inpsyde\MultilingualPress\Framework\Cache\Server\ItemLogic;
use Inpsyde\MultilingualPress\Framework\Cache\Server\Server;
use Inpsyde\MultilingualPress\Framework\Service\IntegrationServiceProvider;
use Inpsyde\MultilingualPress\Framework\WordpressContext;
use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository;
use Inpsyde\MultilingualPress\Database\Table\ContentRelationsTable;
use Inpsyde\MultilingualPress\Database\Table\LanguagesTable;
use Inpsyde\MultilingualPress\Database\Table\RelationshipsTable;
use Inpsyde\MultilingualPress\Database\Table\SiteRelationsTable;
use Inpsyde\MultilingualPress\Framework\Factory\LanguageFactory;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\ServiceProvider as BaseServiceProvider;

use function Inpsyde\MultilingualPress\resolve;

/**
 * Service provider for all API objects.
 */
final class ServiceProvider implements BaseServiceProvider, IntegrationServiceProvider
{
    /**
     * @inheritdoc
     * phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
     */
    public function register(Container $container)
    {
        // phpcs:enable

        $container->share(
            ContentRelations::class,
            static function (Container $container): WpdbContentRelations {
                return new WpdbContentRelations(
                    $container[\wpdb::class],
                    $container[ContentRelationsTable::class],
                    $container[RelationshipsTable::class],
                    $container[ActivePostTypes::class],
                    $container[ActiveTaxonomies::class],
                    new Facade($container[Server::class], ContentRelations::class),
                    $container[CacheSettingsRepository::class],
                    $container[SiteSettingsRepository::class],
                    $container[SiteRelations::class]
                );
            }
        );

        $container->share(
            Languages::class,
            static function (Container $container): WpdbLanguages {
                return new WpdbLanguages(
                    $container[\wpdb::class],
                    $container[LanguagesTable::class],
                    $container[SiteSettingsRepository::class],
                    $container[LanguageFactory::class]
                );
            }
        );

        $container->share(
            SiteRelations::class,
            static function (Container $container): WpdbSiteRelations {
                return new WpdbSiteRelations(
                    $container[\wpdb::class],
                    $container[SiteRelationsTable::class],
                    new Facade($container[Server::class], SiteRelations::class),
                    $container[CacheSettingsRepository::class]
                );
            }
        );

        $container->share(
            FrameworkTranslations::class,
            static function (Container $container): FrameworkTranslations {
                return new Translations(
                    $container[SiteRelations::class],
                    $container[ContentRelations::class],
                    $container[Languages::class],
                    $container[WordpressContext::class],
                    new Facade($container[Server::class], Translations::class),
                    $container[CacheSettingsRepository::class]
                );
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function integrate(Container $container)
    {
        $this->integrateCache($container);
    }

    /**
     * @param Container $container
     */
    private function integrateCache(Container $container)
    {
        $this->integrateRelationsCache($container);

        $this->integrateContentRelationsCache($container);

        $this->integrateTranslationCache($container);
    }

    /**
     * @param Container $container
     */
    private function integrateRelationsCache(Container $container)
    {
        $allRelationsCacheLogic = new ItemLogic(
            SiteRelations::class,
            SiteRelations::ALL_RELATIONS_CACHE_KEY
        );
        $allRelationsCacheLogic->updateWith(
            [$container[SiteRelations::class], SiteRelations::ALL_RELATIONS_CACHE_KEY]
        );

        $relatedSiteIdsCacheLogic = new ItemLogic(
            SiteRelations::class,
            SiteRelations::RELATED_SITE_IDS_CACHE_KEY
        );
        $relatedSiteIdsCacheLogic->updateWith(
            static function (int $siteId, bool $includeSite) use ($container): array {
                return $container[SiteRelations::class]->relatedSiteIds($siteId, $includeSite);
            }
        );

        $container[Server::class]
            ->registerForNetwork($allRelationsCacheLogic)
            ->registerForNetwork($relatedSiteIdsCacheLogic);
    }

    /**
     * @param Container $container
     */
    private function integrateContentRelationsCache(Container $container)
    {
        $contentRelationsCacheLogic = new ItemLogic(
            ContentRelations::class,
            ContentRelations::HAS_SITE_RELATIONS_CACHE_KEY
        );
        $contentRelationsCacheLogic->updateWith(
            static function (int $siteId, string $type = '') use ($container): bool {
                return $container[ContentRelations::class]->hasSiteRelations($siteId, $type);
            }
        );

        $contentIdsCacheLogic = new ItemLogic(
            ContentRelations::class,
            ContentRelations::CONTENT_IDS_CACHE_KEY
        );
        $contentIdsCacheLogic->updateWith(
            static function (int $relationshipId) use ($container): array {
                return $container[ContentRelations::class]->contentIds($relationshipId);
            }
        );

        $contentRelationCacheLogic = new ItemLogic(
            ContentRelations::class,
            ContentRelations::RELATIONS_CACHE_KEY
        );
        $contentRelationCacheLogic->updateWith(
            static function (int $siteId, int $contentId, string $type) use ($container): array {
                return $container[ContentRelations::class]->relations($siteId, $contentId, $type);
            }
        );

        $container[Server::class]
            ->registerForNetwork($contentRelationsCacheLogic)
            ->registerForNetwork($contentIdsCacheLogic)
            ->registerForNetwork($contentRelationCacheLogic);
    }

    /**
     * @param Container $container
     */
    private function integrateTranslationCache(Container $container)
    {
        $translationsCacheLogic =
            (new ItemLogic(Translations::class, Translations::SEARCH_CACHE_KEY))
                ->updateWith(static function (array $translationArgs): array {
                    $translationArgs = new TranslationSearchArgs($translationArgs);

                    return resolve(FrameworkTranslations::class)->searchTranslations($translationArgs);
                });

        $container[Server::class]
            ->registerForNetwork($translationsCacheLogic);
    }
}
