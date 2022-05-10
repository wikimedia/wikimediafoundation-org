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
use Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
use Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
use Inpsyde\MultilingualPress\Schedule\Schedule;
use Inpsyde\MultilingualPress\Schedule\Scheduler;
use Throwable;

class AttachmentDuplicatorHandler
{
    use SwitchSiteTrait;
    use ThrowableHandleCapableTrait;

    /**
     * @var SiteScheduleOption
     */
    private $option;

    /**
     * @var Attachment\Duplicator
     */
    private $attachmentDuplicator;

    /**
     * @var Scheduler
     */
    private $scheduler;

    /**
     * @var Attachment\Collection
     */
    private $attachmentCollection;

    /**
     * @var Attachment\DatabaseDataReplacer
     */
    private $dataBaseDataReplacer;

    /**
     * AttachmentDuplicatorHandler constructor.
     * @param SiteScheduleOption $option
     * @param Attachment\Duplicator $attachmentDuplicator
     * @param Attachment\Collection $attachmentCollection
     * @param Scheduler $scheduler
     * @param Attachment\DatabaseDataReplacer $dataBaseDataReplacer
     */
    public function __construct(
        SiteScheduleOption $option,
        Attachment\Duplicator $attachmentDuplicator,
        Attachment\Collection $attachmentCollection,
        Scheduler $scheduler,
        Attachment\DatabaseDataReplacer $dataBaseDataReplacer
    ) {

        $this->option = $option;
        $this->attachmentCollection = $attachmentCollection;
        $this->attachmentDuplicator = $attachmentDuplicator;
        $this->scheduler = $scheduler;
        $this->dataBaseDataReplacer = $dataBaseDataReplacer;
    }

    /**
     * Handle the cron job request by copy an entire directory of attachments.
     * The $step identify the current directory to copy within the uploads directory.
     *
     * @wp-hook AttachmentDuplicatorScheduler::SCHEDULE_HOOK
     *
     * @param \stdClass $scheduleArgs
     *
     * @return bool
     * @throws Throwable
     */
    public function handle(\stdClass $scheduleArgs): bool
    {
        try {
            // Turn installing off because of problems in VipGo
            $wpInstalling = wp_installing(false);

            list($schedule, $step) = $this->scheduler->parseScheduleHookParam($scheduleArgs);

            if (!$schedule instanceof Schedule || $schedule->isDone()) {
                return false;
            }

            $args = (object)$scheduleArgs->args;
            if (!$args) {
                return false;
            }

            $sourceSiteId = (int)$args->sourceSiteId;
            $newSiteId = (int)$args->newSiteId;

            $scheduleId = $this->option->readForSite($newSiteId);

            if ($sourceSiteId === $newSiteId) {
                return false;
            }

            $attachments = $this->sourceAttachmentList($sourceSiteId, $step);

            if (!$attachments) {
                $this->forceScheduleDoneStatus($schedule);
                $this->option->deleteForSite($newSiteId);
                return false;
            }

            //  Do it after the attachments have been checked. May be some schedule need be closed.
            if ($scheduleId !== $schedule->id()) {
                return false;
            }

            $duplicated = $this->duplicate($args, $attachments);

            $this->scheduler->stepDone($schedule);

            if ($schedule->isDone()) {
                $this->dataBaseDataReplacer->replaceUrlsForSites($sourceSiteId, $newSiteId);
                $this->option->deleteForSite($newSiteId);
                $this->scheduler->cleanup($schedule);
            }

            wp_installing($wpInstalling);

            return $duplicated;
        } catch (Throwable $throwable) {
            $this->handleThrowable($throwable);
        }
    }

    /**
     * Retrieve the List of the Attachments for the Source Site
     *
     * @param int $sourceSiteId
     * @param int $step
     * @return array
     */
    private function sourceAttachmentList(int $sourceSiteId, int $step): array
    {
        /**
         * Filter the Default Number of Attachments to Retrieve from Database.
         * Smaller the number more cron event will be stored into the db.
         *
         * @param int AttachmentDuplicatorScheduler::DEFAULT_COLLECTION_LIMIT
         */
        $defaultCollectionLimit = (int)apply_filters(
            AttachmentDuplicatorScheduler::FILTER_DEFAULT_COLLECTION_LIMIT,
            AttachmentDuplicatorScheduler::DEFAULT_COLLECTION_LIMIT
        );

        $currentSiteId = $this->maybeSwitchSite($sourceSiteId);
        $attachments = $this->attachmentCollection->list(
            $step * $defaultCollectionLimit,
            $defaultCollectionLimit
        );
        $this->maybeRestoreSite($currentSiteId);

        return $attachments;
    }

    /**
     * @param \stdClass $args
     * @param array $attachments
     * @return bool
     */
    private function duplicate(\stdClass $args, array $attachments): bool
    {
        $duplicated = 0;
        foreach ($attachments as $attachmentData) {
            $attachmentsDirectory = $attachmentData['dir'] ?? '';
            $attachmentsFiles = $attachmentData['files'] ?? [];

            if (!$attachmentsDirectory || !$attachmentsFiles) {
                continue;
            }

            $duplicateSuccess = $this->attachmentDuplicator->duplicateAttachmentsFromSite(
                $args->sourceSiteId,
                $args->newSiteId,
                [$attachmentsDirectory => $attachmentsFiles]
            );

            $duplicateSuccess and ++$duplicated;
        }

        return $duplicated === count($attachments);
    }

    /**
     * Force the status for the given schedule to Done and clean up the scheduler
     *
     * @param Schedule $schedule
     */
    private function forceScheduleDoneStatus(Schedule $schedule)
    {
        $schedule->done();
        $this->scheduler->cleanup($schedule);
    }
}
