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

use Inpsyde\MultilingualPress\Attachment;
use Inpsyde\MultilingualPress\Schedule\Scheduler;
use UnexpectedValueException;

/**
 * Class AttachmentDuplicatorScheduler
 * @package Inpsyde\MultilingualPress\SiteDuplication
 */
class AttachmentDuplicatorScheduler
{
    const FILTER_DEFAULT_COLLECTION_LIMIT = 'multilingualpress.attachment_duplicator_default_limit';
    const DEFAULT_COLLECTION_LIMIT = 100;
    const SCHEDULE_HOOK = 'multilingualpress.site_attachments_duplicator';

    /**
     * @var SiteScheduleOption
     */
    private $option;

    /**
     * @var Attachment\Collection
     */
    private $attachmentsCollection;

    /**
     * @var Scheduler
     */
    private $scheduler;

    /**
     * AttachmentDuplicatorScheduler constructor.
     * @param SiteScheduleOption $option
     * @param Attachment\Collection $attachmentsCollection
     * @param Scheduler $scheduler
     */
    public function __construct(
        SiteScheduleOption $option,
        Attachment\Collection $attachmentsCollection,
        Scheduler $scheduler
    ) {

        $this->option = $option;
        $this->attachmentsCollection = $attachmentsCollection;
        $this->scheduler = $scheduler;
    }

    /**
     * Schedule a new set of cron jobs to copy source site attachments into the new given site.
     *
     * @param int $sourceSiteId
     * @param int $newSiteId
     * @throws UnexpectedValueException
     */
    public function schedule(int $sourceSiteId, int $newSiteId)
    {
        if ($sourceSiteId === $newSiteId) {
            return;
        }

        /**
         * Filter the Default Number of Attachments to Retrieve from Database
         *
         * @param int self::DEFAULT_COLLECTION_LIMIT
         */
        $defaultCollectionLimit = apply_filters(
            self::FILTER_DEFAULT_COLLECTION_LIMIT,
            self::DEFAULT_COLLECTION_LIMIT
        );

        if (!is_numeric($defaultCollectionLimit)) {
            return;
        }

        $option = $this->option->readForSite($newSiteId);
        $attachmentsNumber = $this->attachmentsCollection->count();

        if (!$attachmentsNumber || $option) {
            return;
        }

        /*
         * Steps are increased by one to ensure all of the attachments are copied when the division
         * doesn't produce an integer value or when it's zero.
         */
        $steps = (int)ceil($attachmentsNumber / $defaultCollectionLimit);
        $steps or $steps = 1;

        $scheduleId = $this->scheduler->newSchedule(
            $steps,
            self::SCHEDULE_HOOK,
            compact('sourceSiteId', 'newSiteId', 'attachmentsNumber')
        );

        $scheduleId and $this->option->createForSite($newSiteId, $scheduleId);
    }
}
