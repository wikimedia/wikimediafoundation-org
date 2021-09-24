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

namespace Inpsyde\MultilingualPress\Factory;

use Inpsyde\MultilingualPress\Framework\Factory\ClassResolver;
use Inpsyde\MultilingualPress\Framework\Factory\ErrorFactory;
use Inpsyde\MultilingualPress\Framework\Factory\LanguageFactory;
use Inpsyde\MultilingualPress\Framework\Factory\NonceFactory;
use Inpsyde\MultilingualPress\Framework\Factory\UrlFactory;
use Inpsyde\MultilingualPress\Framework\Language\Language as FrameworkLanguage;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use Inpsyde\MultilingualPress\Framework\Nonce\WpNonce;
use Inpsyde\MultilingualPress\Framework\Url\EscapedUrl;
use Inpsyde\MultilingualPress\Framework\Url\Url;
use Inpsyde\MultilingualPress\Framework\Service\Container;
use Inpsyde\MultilingualPress\Framework\Service\ServiceProvider as FrameworkServiceProvider;
use Inpsyde\MultilingualPress\Language\Language;

/**
 * Service provider for all factories.
 */
final class ServiceProvider implements FrameworkServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register(Container $container)
    {
        $container->share(
            ErrorFactory::class,
            static function (): ErrorFactory {
                return new ErrorFactory(new ClassResolver(\WP_Error::class));
            }
        );

        $container->share(
            NonceFactory::class,
            static function (): NonceFactory {
                return new NonceFactory(new ClassResolver(Nonce::class, WpNonce::class));
            }
        );

        $container->share(
            LanguageFactory::class,
            static function (): LanguageFactory {
                return new LanguageFactory(
                    new ClassResolver(FrameworkLanguage::class, Language::class)
                );
            }
        );

        $container->share(
            UrlFactory::class,
            static function (): UrlFactory {
                return new UrlFactory(new ClassResolver(Url::class, EscapedUrl::class));
            }
        );
    }
}
