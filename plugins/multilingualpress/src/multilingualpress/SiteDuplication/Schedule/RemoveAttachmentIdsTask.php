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
use Inpsyde\MultilingualPress\Schedule\Action\ActionTask;
use Throwable;
use UnexpectedValueException;

/**
 * Class RemoveAttachmentIdsTask
 * @package Inpsyde\MultilingualPress\SiteDuplication\Schedule\Action
 */
class RemoveAttachmentIdsTask implements ActionTask
{
    use ThrowableHandleCapableTrait;
    use SiteIdValidatorTrait;

    /**
     * @var SiteScheduleOption
     */
    private $siteScheduleOption;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $siteIdNameInRequest;

    /**
     * AttachmentsScheduleIdsRemoveAction constructor.
     * @param Request $request
     * @param SiteScheduleOption $siteScheduleOption
     * @param string $siteIdNameInRequest
     */
    public function __construct(
        Request $request,
        SiteScheduleOption $siteScheduleOption,
        string $siteIdNameInRequest
    ) {

        $this->request = $request;
        $this->siteScheduleOption = $siteScheduleOption;
        $this->siteIdNameInRequest = $siteIdNameInRequest;
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function execute()
    {
        $siteId = $this->siteIdByRequest();
        $this->siteScheduleOption->deleteForSite($siteId);
    }

    /**
     * @return int
     * @throws UnexpectedValueException
     */
    protected function siteIdByRequest(): int
    {
        $siteId = (int)$this->request->bodyValue(
            $this->siteIdNameInRequest,
            INPUT_POST,
            FILTER_SANITIZE_NUMBER_INT
        );

        $this->siteIdMustBeGreaterThanZero($siteId);

        return $siteId;
    }
}
