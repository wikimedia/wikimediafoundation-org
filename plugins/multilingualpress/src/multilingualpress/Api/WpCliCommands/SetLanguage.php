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

namespace Inpsyde\MultilingualPress\Api\WpCliCommands;

use Inpsyde\MultilingualPress\Core\Admin\SiteSettingsRepository;
use Inpsyde\MultilingualPress\WpCli\WpCliCommand;
use Inpsyde\MultilingualPress\WpCli\WpCliCommandsHelper;
use ReflectionClass;
use WP_CLI;

use function Inpsyde\MultilingualPress\siteExists;

/**
 * WP-CLI Set Language.
 */
class SetLanguage implements WpCliCommand
{
    /**
     * @var WpCliCommandsHelper
     */
    protected $wpCliCommandsHelper;
    /**
     * @var SiteSettingsRepository
     */
    private $repository;

    /**
     * @var array<string> A list of available MLP language BCP-47 codes
     */
    private $availableMlpLanguages;

    /**
     * SetLanguage constructor.
     *
     * @param SiteSettingsRepository $repository
     * @param array<string> $availableMlpLanguages A list of available MLP language BCP-47 codes
     * @param WpCliCommandsHelper $wpCliCommandsHelper
     */
    public function __construct(
        SiteSettingsRepository $repository,
        array $availableMlpLanguages,
        WpCliCommandsHelper $wpCliCommandsHelper
    ) {

        $this->repository = $repository;
        $this->availableMlpLanguages = $availableMlpLanguages;
        $this->wpCliCommandsHelper = $wpCliCommandsHelper;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return lcfirst((new ReflectionClass($this))->getShortName());
    }

    /**
     * The handler of
     * {@link https://make.wordpress.org/cli/handbook/references/internal-api/wp-cli-add-command/ WP_CLI::add_command}
     * implementation
     *
     * @param array<string> $args The list of positional arguments
     * @param array<string, scalar> $associativeArgs A map of associative argument names to values
     * @psalm-param array{site-id: string, language: string} $associativeArgs
     * A map of associative argument names to values
     * @return void
     * @throws WP_CLI\ExitException
     */
    public function handler(array $args, array $associativeArgs): void
    {
        $siteId = (int)$associativeArgs['site-id'];
        $language = $associativeArgs['language'];

        if (!siteExists($siteId)) {
            $message = __("The site with given site id doesn't exist", 'multilingualpress');
            $this->wpCliCommandsHelper->showCliError($message);
        }

        $successMessage = sprintf(
            /* translators: %1$s: Site ID for which the language is changed. %2$s: Changed language code. */
            __('The language of site %1$s has been changed to %2$s', 'multilingualpress'),
            $siteId,
            $language
        );

        $errorMessage = sprintf(
        /* translators: %1$s: Site ID for which the language is changed. %2$s: Changed language code. */
            __('Could not update the language of site %1$s to %2$s', 'multilingualpress'),
            $siteId,
            $language
        );

        $this->repository->updateLanguage($language, $siteId)
            ? $this->wpCliCommandsHelper->showCliSuccess($successMessage)
            : $this->wpCliCommandsHelper->showCliError($errorMessage);
    }

    /**
     * @inheritDoc
     */
    public function docs(): array
    {
        return [
            'shortdesc' => 'Set site MLP language',
            'synopsis' => [
                [
                    'type' => 'assoc',
                    'name' => 'site-id',
                    'description' => __('The id of the site to change the language', 'multilingualpress'),
                    'optional' => false,
                ],
                [
                    'type' => 'assoc',
                    'name' => 'language',
                    'description' => __('The language locale code', 'multilingualpress'),
                    'optional' => false,
                    'options' => $this->availableMlpLanguages,
                ],
            ],
            'longdesc' =>   '## EXAMPLES' . "\n\n" . 'wp mlp setLanguage --site-id=1 --language=en-US',
        ];
    }
}
