<?php # -*- coding: utf-8 -*-
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

use Inpsyde\MultilingualPress\Framework\Admin\AdminNotice;
use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\Asset\AssetException;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use function Inpsyde\MultilingualPress\isWpDebugMode;

/**
 * Onboarding messages manager.
 */
class Onboarding
{
    const OPTION_ONBOARDING_DISMISSED = 'onboarding_dismissed';

    /**
     * @var AssetManager
     */
    private $assetManager;

    /**
     * @var Notice
     */
    private $onboardingMessages;

    /**
     * @var State
     */
    private $onboardingState;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var SiteRelations
     */
    private $siteRelations;

    /**
     * @param AssetManager $assetManager
     * @param SiteRelations $siteRelations
     * @param Request $request
     * @param State $onboardingState
     * @param Notice $onboardingMessages
     */
    public function __construct(
        AssetManager $assetManager,
        SiteRelations $siteRelations,
        Request $request,
        State $onboardingState,
        Notice $onboardingMessages
    ) {

        $this->assetManager = $assetManager;
        $this->siteRelations = $siteRelations;
        $this->request = $request;
        $this->onboardingState = $onboardingState;
        $this->onboardingMessages = $onboardingMessages;
    }

    /**
     * @return void
     * @throws AssetException
     * @throws NonexistentTable
     */
    public function messages()
    {
        if (!$this->mayDisplayMessage()) {
            return;
        }

        $siteRelations = $this->siteRelations->allRelations();
        $onboardingState = $this->onboardingState->update(
            $this->onboardingState->read(),
            $siteRelations
        );
        $messageContent = $this->onboardingMessages->onboardingMessageContent(
            $onboardingState
        );

        try {
            $this->enqueueAssets();
        } catch (AssetException $exc) {
            if (isWpDebugMode()) {
                throw $exc;
            }
        }

        AdminNotice::multilingualpress($messageContent->message)
            ->withTitle($messageContent->title)
            ->makeDismissible()
            ->inAllScreens()
            ->render();
    }

    /**
     * @return void
     */
    public function handleDismissOnboardingMessage()
    {
        $onboardingDismissed = $this->request->bodyValue(
            self::OPTION_ONBOARDING_DISMISSED,
            INPUT_GET,
            FILTER_SANITIZE_STRING
        );

        if ($onboardingDismissed === '1' && current_user_can('create_sites')) {
            update_site_option(self::OPTION_ONBOARDING_DISMISSED, true);
        }
    }

    /**
     * @return void
     */
    public static function handleAjaxDismissOnboardingMessage()
    {
        if (!wp_doing_ajax()) {
            return;
        }

        if (!doing_action('wp_ajax_onboarding_plugin')) {
            wp_send_json_error('Invalid action.');
        }

        if (update_site_option(self::OPTION_ONBOARDING_DISMISSED, true)) {
            wp_send_json_success();
        }

        wp_send_json_error('Not updated.');
    }

    /**
     * @return bool
     */
    private function mayDisplayMessage(): bool
    {
        if (!current_user_can('create_sites')) {
            return false;
        }

        if ((bool)get_site_option(self::OPTION_ONBOARDING_DISMISSED) === true) {
            return false;
        }

        return true;
    }

    /**
     * @return void
     * @throws AssetException
     */
    private function enqueueAssets()
    {
        $this->assetManager->enqueueScript('onboarding');
        $this->assetManager->enqueueStyle('multilingualpress-admin');
    }
}
