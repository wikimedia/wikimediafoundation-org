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

namespace Inpsyde\MultilingualPress\Core\Admin\Settings\Cache;

use Inpsyde\MultilingualPress\Core\Admin\Settings\SettingsUpdater;
use Inpsyde\MultilingualPress\Framework\Auth\Auth;
use Inpsyde\MultilingualPress\Framework\Http\Request;

use function Inpsyde\MultilingualPress\stringToBool;

/**
 * Class CacheSettingsUpdater
 * @package Inpsyde\MultilingualPress\Core\Admin
 */
class CacheSettingsUpdater implements SettingsUpdater
{
    /**
     * @var CacheSettingsRepository
     */
    private $cacheSettingsRepository;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var CacheSettingsOptions
     */
    private $cacheSettingsOptions;

    /**
     * CacheSettingsUpdater constructor.
     * @param CacheSettingsRepository $cacheSettingsRepository
     * @param Auth $auth
     * @param CacheSettingsOptions $cacheSettingsOptions
     */
    public function __construct(
        CacheSettingsRepository $cacheSettingsRepository,
        Auth $auth,
        CacheSettingsOptions $cacheSettingsOptions
    ) {

        $this->cacheSettingsRepository = $cacheSettingsRepository;
        $this->auth = $auth;
        $this->cacheSettingsOptions = $cacheSettingsOptions;
    }

    /**
     * @inheritDoc
     */
    public function updateSettings(Request $request): bool
    {
        if (!$this->auth->isAuthorized()) {
            return false;
        }

        $settings = $this->retrieveValueFromRequest($request);

        return $this->cacheSettingsRepository->update($settings);
    }

    /**
     * Retrieve Values From Request
     *
     * @param Request $request
     * @return array
     */
    protected function retrieveValueFromRequest(Request $request): array
    {
        $settings = (array)$request->bodyValue(
            CacheSettingsRepository::OPTION_NAME,
            INPUT_POST,
            FILTER_DEFAULT,
            FILTER_FORCE_ARRAY
        );

        return $this->normalizeValues($settings);
    }

    /**
     * Normalize Values By Converting Strings into Boolean
     *
     * The submitted value for checkboxes it's usually a 'on' or nothing,
     * we don't want to have an 'on' value into the database because there's no
     * off for them, simply non submitted values are no in $_POST request so we
     * do not store them into database.
     *
     * @param array $settings
     * @return array
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    protected function normalizeValues(array $settings): array
    {
        // phpcs:enable

        $defaults = $this->cacheSettingsOptions->defaults();

        foreach ($defaults as $groupName => $group) {
            foreach ($group as $key => $value) {
                if (isset($settings[$groupName][$key])) {
                    $settings[$groupName][$key] = stringToBool($settings[$groupName][$key]);
                    continue;
                }

                /*
                 * Set the not submitted value to false
                 *
                 * This way all of the settings will be stored with the correct value.
                 */
                $settings[$groupName][$key] = false;
            }
        }

        return $settings;
    }
}
