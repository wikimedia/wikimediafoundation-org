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

use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\SiteIdValidatorTrait;
use Inpsyde\MultilingualPress\Framework\ThrowableHandleCapableTrait;
use Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicator;
use Throwable;

/**
 * Class MaybeScheduleAttachmentDuplication
 * @package Inpsyde\MultilingualPress\SiteDuplication\Schedule
 */
class MaybeScheduleAttachmentDuplication
{
    use ThrowableHandleCapableTrait;
    use SiteIdValidatorTrait;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var AttachmentDuplicatorScheduler
     */
    private $attachmentDuplicatorScheduler;

    /**
     * MaybeScheduleAttachmentDuplication constructor.
     * @param Request $request
     * @param AttachmentDuplicatorScheduler $attachmentDuplicatorScheduler
     */
    public function __construct(
        Request $request,
        AttachmentDuplicatorScheduler $attachmentDuplicatorScheduler
    ) {

        $this->request = $request;
        $this->attachmentDuplicatorScheduler = $attachmentDuplicatorScheduler;
    }

    /**
     * Schedule the attachment duplication if requested
     *
     * @param int $sourceSiteId
     * @param int $newSiteId
     * @throws Throwable
     */
    public function maybeScheduleAttachmentsDuplication(int $sourceSiteId, int $newSiteId)
    {
        $this->siteIdMustBeGreaterThanZero($sourceSiteId);
        $this->siteIdMustBeGreaterThanZero($newSiteId);

        $wantToCopyAttachments = (bool)$this->request->bodyValue(
            SiteDuplicator::NAME_COPY_ATTACHMENTS,
            INPUT_POST,
            FILTER_VALIDATE_BOOLEAN
        );

        if (!$wantToCopyAttachments) {
            return;
        }

        $this->scheduleAttachmentDuplication($sourceSiteId, $newSiteId);
    }

    /**
     * @param int $sourceSiteId
     * @param int $newSiteId
     * @throws Throwable
     */
    protected function scheduleAttachmentDuplication(int $sourceSiteId, int $newSiteId)
    {
        try {
            $this->attachmentDuplicatorScheduler->schedule(
                $sourceSiteId,
                $newSiteId
            );
        } catch (Throwable $throwable) {
            $this->logThrowable($throwable);
        }
    }
}
