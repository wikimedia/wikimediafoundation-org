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

namespace Inpsyde\MultilingualPress\SiteDuplication\Schedule;

use Inpsyde\MultilingualPress\Framework\SiteIdValidatorTrait;
use UnexpectedValueException;

/**
 * Class SiteScheduleOption
 *
 * @package Inpsyde\MultilingualPress\SiteDuplication
 */
class SiteScheduleOption
{
    use SiteIdValidatorTrait;

    const OPTION_SCHEDULE_IDS = 'multilingualpress.schedule_option_ids';

    /**
     * Create new schedule id for the given site
     *
     * @param int $siteId
     * @param string $scheduleId
     * @return bool
     * @throws UnexpectedValueException
     */
    public function createForSite(int $siteId, string $scheduleId): bool
    {
        $this->siteIdMustBeGreaterThanZero($siteId);

        $allOptions = $this->allSchedule();

        if (\in_array($scheduleId, $allOptions, true)) {
            return false;
        }

        $allOptions[$siteId] = $scheduleId;

        // Turn installing off because of problems in VipGo
        $wpInstalling = wp_installing(false);
        $option = $this->updateScheduleId($allOptions);
        wp_installing($wpInstalling);

        return $option;
    }

    /**
     * Retrieve the schedule id for the given site
     *
     * @param int $siteId
     * @return string
     * @throws UnexpectedValueException
     */
    public function readForSite(int $siteId): string
    {
        $this->siteIdMustBeGreaterThanZero($siteId);

        $allOptions = $this->allSchedule();
        return $allOptions[$siteId] ?? '';
    }

    /**
     * Delete the schedule id for the given site
     *
     * @param int $siteId
     * @return bool
     * @throws UnexpectedValueException
     */
    public function deleteForSite(int $siteId): bool
    {
        $this->siteIdMustBeGreaterThanZero($siteId);

        $allOptions = $this->allSchedule();

        if (!\array_key_exists($siteId, $allOptions)) {
            return true;
        }

        unset($allOptions[$siteId]);

        if (empty($allOptions)) {
            return delete_option(self::OPTION_SCHEDULE_IDS);
        }

        return $this->updateScheduleId($allOptions);
    }

    /**
     * Retrieve all schedule
     *
     * @return array
     */
    public function allSchedule(): array
    {
        return array_filter((array)get_option(self::OPTION_SCHEDULE_IDS));
    }

    /**
     * Update Schedule Id Option
     *
     * @param array $options
     * @return bool
     */
    private function updateScheduleId(array $options): bool
    {
        return update_option(self::OPTION_SCHEDULE_IDS, $options, false);
    }
}
