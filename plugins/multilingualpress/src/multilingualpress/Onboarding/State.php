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

namespace Inpsyde\MultilingualPress\Onboarding;

use Inpsyde\MultilingualPress\Core\Admin\Screen;

/**
 * Onboarding state manager.
 */
class State
{
    const OPTION_NAME = 'onboarding_state';
    const STATE_SITES = 'sites';
    const STATE_SETTINGS = 'settings';
    const STATE_POST = 'post';
    const STATE_END = 'end';

    /**
     * Update onboarding state based on site relations and screen.
     * @param string $onboardingState
     * @param array $siteRelations
     * @return string
     */
    public function update(
        string $onboardingState,
        array $siteRelations
    ): string {

        if (count($siteRelations) > 0 && $onboardingState === self::STATE_SITES) {
            return $this->updateForSettings();
        }

        if (
            Screen::isMultilingualPressSettings()
            && $onboardingState === self::STATE_SETTINGS
        ) {
            return $this->updateForPost();
        }

        if (Screen::isEditPostsTable() && $onboardingState === self::STATE_POST) {
            return $this->finish();
        }

        return $onboardingState;
    }

    /**
     * @return string
     */
    public function read(): string
    {
        return (string)get_site_option(self::OPTION_NAME, self::STATE_SITES);
    }

    /**
     * @return string
     */
    private function updateForSettings(): string
    {
        update_site_option(self::OPTION_NAME, self::STATE_SETTINGS);

        return self::STATE_SETTINGS;
    }

    /**
     * @return string
     */
    private function updateForPost(): string
    {
        update_site_option(self::OPTION_NAME, self::STATE_POST);

        return self::STATE_POST;
    }

    /**
     * @return string
     */
    private function finish(): string
    {
        update_site_option(self::OPTION_NAME, self::STATE_END);

        return self::STATE_END;
    }
}
