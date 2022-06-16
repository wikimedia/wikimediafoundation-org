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

use Inpsyde\MultilingualPress\Framework\Asset\AssetException;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Factory\NonceFactory;
use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use Inpsyde\MultilingualPress\Schedule\AjaxScheduleHandler;
use Inpsyde\MultilingualPress\SiteDuplication\ServiceProvider;

use function Inpsyde\MultilingualPress\isWpDebugMode;

/**
 * Class ScheduleAssetManager
 * @package Inpsyde\MultilingualPress\SiteDuplication
 */
class ScheduleAssetManager
{
    const NAME_ATTACHMENT_SCHEDULE_ID = 'scheduleId';
    const NAME_SITE_ID = 'siteId';

    /**
     * @var SiteScheduleOption
     */
    private $siteScheduleOption;

    /**
     * @var AjaxScheduleHandler
     */
    private $ajaxScheduleHandler;

    /**
     * @var AssetManager
     */
    private $assetManager;

    /**
     * @var NonceFactory
     */
    private $scheduleActionsNonce;

    /**
     * ScheduleAssetManager constructor.
     * @param SiteScheduleOption $siteScheduleOption
     * @param AjaxScheduleHandler $ajaxScheduleHandler
     * @param AssetManager $assetManager
     * @param Nonce $scheduleActionsNonce
     */
    public function __construct(
        SiteScheduleOption $siteScheduleOption,
        AjaxScheduleHandler $ajaxScheduleHandler,
        AssetManager $assetManager,
        Nonce $scheduleActionsNonce
    ) {

        $this->siteScheduleOption = $siteScheduleOption;
        $this->ajaxScheduleHandler = $ajaxScheduleHandler;
        $this->assetManager = $assetManager;
        $this->scheduleActionsNonce = $scheduleActionsNonce;
    }

    /**
     * Enqueue and Localize the main plugin script
     *
     * @return void
     * @throws AssetException
     */
    public function enqueueScript()
    {
        $scheduleUrl = $this->scheduleUrl();

        try {
            $this->assetManager->enqueueScriptWithData(
                'multilingualpress-site-duplication-admin',
                'siteDuplicatorScheduleData',
                [
                    'ajaxUrl' => esc_url(admin_url('admin-ajax.php')),
                    'scheduleUrl' => $scheduleUrl,
                    'attachmentDuplicatorTranslations' => $this->attachmentDuplicatorTranslations(),
                    'attachmentDuplicatorActions' => $this->attachmentDuplicatorActions(),
                ]
            );
        } catch (AssetException $exc) {
            if (isWpDebugMode()) {
                throw $exc;
            }
        }
    }

    /**
     * Retrieve the ajax schedule information url to call to obtain information about the current
     * status of the cron jobs
     *
     * @return string
     */
    protected function scheduleUrl(): string
    {
        $allSchedule = $this->siteScheduleOption->allSchedule();
        $schedule = reset($allSchedule) ?: '';
        return $this->ajaxScheduleHandler->scheduleInfoAjaxUrl($schedule);
    }

    /**
     * @return array
     */
    protected function attachmentDuplicatorTranslations(): array
    {
        return [
            'doNotCloseBrowserTabMessage' => esc_html__(
                'You cannot close this window until the entire process is finished.',
                'multilingualpress'
            ),
            'scheduleInfoErrorMessage' => esc_html__(
                'Something went wrong when trying to retrieve information about media copy task. Please, wait a minute or try to reload the page. If the problem persists please contact our support.',
                'multilingualpress'
            ),
            'preventFormSubmissionMessage' => esc_html__(
                'Actually one or more scheduled jobs are running, please wait until everything has been completed.',
                'multilingualpress'
            ),
        ];
    }

    /**
     * @return array
     */
    protected function attachmentDuplicatorActions(): array
    {
        return [
            'nonce' => (string)$this->scheduleActionsNonce,
            'nonceAction' => $this->scheduleActionsNonce->action(),
            'nameAjaxHook' => ServiceProvider::SCHEDULE_ACTION_ATTACHMENTS_AJAX_HOOK_NAME,
        ];
    }
}
