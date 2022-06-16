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

namespace Inpsyde\MultilingualPress\Translator;

use Inpsyde\MultilingualPress\Core\Admin;
use Inpsyde\MultilingualPress\Core\PostTypeRepository;
use Inpsyde\MultilingualPress\Core\TaxonomyRepository;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Api\Translations;
use Inpsyde\MultilingualPress\Core\Entity\ActivePostTypes;
use Inpsyde\MultilingualPress\Framework\Factory\UrlFactory;
use Inpsyde\MultilingualPress\Framework\WordpressContext;
use Inpsyde\MultilingualPress\Framework\Service\BootstrappableServiceProvider;
use Inpsyde\MultilingualPress\Framework\Service\Container;

/**
 * Service provider for all translation objects.
 */
final class ServiceProvider implements BootstrappableServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $this->registerContentRelatedTranslations($container);
        $this->registerNotContentRelatedTranslations($container);
    }

    /**
     * @param Container $container
     */
    private function registerContentRelatedTranslations(Container $container)
    {
        $container->addService(
            PostTranslator::class,
            static function (Container $container): PostTranslator {
                return new PostTranslator(
                    $container[PostTypeRepository::class],
                    $container[Admin\PostTypeSlugsSettingsRepository::class],
                    $container[UrlFactory::class]
                );
            }
        );

        $container->addService(
            TermTranslator::class,
            static function (Container $container): TermTranslator {
                return new TermTranslator(
                    $container[TaxonomyRepository::class],
                    $container[UrlFactory::class]
                );
            }
        );
    }

    /**
     * @param Container $container
     */
    private function registerNotContentRelatedTranslations(Container $container)
    {
        $container->addService(
            SearchTranslator::class,
            static function (Container $container): SearchTranslator {
                return new SearchTranslator($container[UrlFactory::class]);
            }
        );

        $container->addService(
            DateTranslator::class,
            static function (Container $container): DateTranslator {
                return new DateTranslator(
                    $container[UrlFactory::class]
                );
            }
        );

        $container->addService(
            PostTypeTranslator::class,
            static function (Container $container): PostTypeTranslator {
                return new PostTypeTranslator(
                    $container[Admin\PostTypeSlugsSettingsRepository::class],
                    $container[UrlFactory::class],
                    $container[ActivePostTypes::class]
                );
            }
        );

        $container->addService(
            HomeTranslator::class,
            static function (Container $container): HomeTranslator {
                return new HomeTranslator(
                    $container[UrlFactory::class],
                    $container[ContentRelations::class]
                );
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function bootstrap(Container $container)
    {
        $translations = $container[Translations::class];

        $translations->registerTranslator(
            $container[HomeTranslator::class],
            WordpressContext::TYPE_HOME
        );

        $this->bootstrapPostTranslator($container, $translations);
        $this->bootstrapTermTranslator($container, $translations);
        $this->bootstrapDateTranslation($container, $translations);

        $translations->registerTranslator(
            $container[PostTypeTranslator::class],
            WordpressContext::TYPE_POST_TYPE_ARCHIVE
        );

        $translations->registerTranslator(
            $container[SearchTranslator::class],
            WordpressContext::TYPE_SEARCH
        );
    }

    /**
     * @param Container $container
     * @param Translations $translations
     */
    private function bootstrapPostTranslator(Container $container, Translations $translations)
    {
        $postTranslator = $container[PostTranslator::class];

        add_action(
            'setup_theme',
            static function () use ($postTranslator) {
                global $wp_rewrite;
                $postTranslator->ensureWpRewrite($wp_rewrite);
            }
        );

        $translations->registerTranslator(
            $postTranslator,
            WordpressContext::TYPE_SINGULAR
        );
    }

    /**
     * @param Container $container
     * @param Translations $translations
     *
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    private function bootstrapTermTranslator(Container $container, Translations $translations)
    {
        // phpcs:enable

        $termTranslator = $container[TermTranslator::class];

        add_action(
            'setup_theme',
            static function () use ($termTranslator) {
                global $wp_rewrite;
                $termTranslator->ensureWpRewrite($wp_rewrite);
            }
        );

        $translations->registerTranslator(
            $termTranslator,
            WordpressContext::TYPE_TERM_ARCHIVE
        );
    }

    /**
     * @param Container $container
     * @param Translations $translations
     */
    private function bootstrapDateTranslation(Container $container, Translations $translations)
    {
        $dateTranslation = $container[DateTranslator::class];
        $translations->registerTranslator(
            $dateTranslation,
            WordpressContext::TYPE_DATE_ARCHIVE
        );

        add_action(
            'setup_theme',
            static function () use ($dateTranslation) {
                global $wp, $wp_rewrite;
                $dateTranslation->ensureWp($wp);
                $dateTranslation->ensureWpRewrite($wp_rewrite);
            }
        );
    }
}
