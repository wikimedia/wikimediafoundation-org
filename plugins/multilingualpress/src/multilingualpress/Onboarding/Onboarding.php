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

use Inpsyde\MultilingualPress\Framework\Admin\AdminNotice;
use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\Asset\AssetException;
use Inpsyde\MultilingualPress\Framework\Asset\AssetManager;
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\Framework\Http\Request;

use function Inpsyde\MultilingualPress\assignedLanguages;
use function Inpsyde\MultilingualPress\isWpDebugMode;

/**
 * Onboarding messages manager.
 */
class Onboarding
{
    const OPTION_ONBOARDING_DISMISSED = 'onboarding_dismissed';
    const OPTION_LANGUAGE_SETTINGS_CHANGED_DISMISSED = 'language_settings_changed_dismissed';

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
    public function handleAjaxDismissOnboardingMessage()
    {
        if (!wp_doing_ajax()) {
            return;
        }

        if (!doing_action('wp_ajax_onboarding_plugin')) {
            wp_send_json_error('Invalid action.');
        }

        $type = $this->request->bodyValue(
            'type',
            INPUT_POST,
            FILTER_SANITIZE_STRING
        );

        if (!empty($type) && update_site_option(self::OPTION_LANGUAGE_SETTINGS_CHANGED_DISMISSED, true)) {
            wp_send_json_success();
            die;
        }

        if (update_site_option(self::OPTION_ONBOARDING_DISMISSED, true)) {
            wp_send_json_success();
            die;
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

    /**
     * Handle onboarding of new language settings.
     *
     * Since we are not overriding the default WordPress language setting we need to show a message about updated
     * features. Besides that when the plugin is updated we need to check existing value for default WordPress language
     * setting and if it doesn't match the MLP language setting value then we need to override it with MLP language
     * setting value. This has to be done only once so that the users will not loose their frontend language.
     * This Functionality will be removed after next release.
     *
     * @throws NonexistentTable
     * phpcs:disable Inpsyde.CodeQuality.NestingLevel.High
     * phpcs:disable Inpsyde.CodeQuality.LineLength.TooLong
     */
    public function handleLanguageSettings()
    {
        if (!$this->mayDisplayMessage()) {
            return;
        }

        $assignedLanguages = assignedLanguages();
        $languagesForNotices = [];
        $mlpCheckLanguages = (bool)get_site_option('mlp_check_languages');
        $noticeDismissed = (bool)get_site_option(self::OPTION_LANGUAGE_SETTINGS_CHANGED_DISMISSED);

        if (!$mlpCheckLanguages) {
            foreach ($assignedLanguages as $siteId => $language) {
                $wpLang = get_blog_option($siteId, 'WPLANG');
                $mlpLanguageLocale = $language->locale();

                if ($wpLang === $mlpLanguageLocale || !in_array($mlpLanguageLocale, get_available_languages(), true)) {
                    continue;
                }

                $languagesForNotices[$siteId]['mlpLanguage'] = $mlpLanguageLocale;
                $languagesForNotices[$siteId]['wpLanguage'] = $wpLang ?? 'en_US';
                update_blog_option($siteId, 'WPLANG', $mlpLanguageLocale);
            }
            update_site_option('mlp_check_languages', true);
        }

        if ($noticeDismissed) {
            return;
        }

        $generalMessage = __('From now on your site frontend language will be decided by default WordPress language
        setting. We have automatically checked if WordPress default language setting matches MultilingualPress language 
        for your existing sites but please recheck the language settings of your sites', 'multilingualpress');

        if (!empty($languagesForNotices)) {
            $langs = '';
            $langMessage = __('</br> The following WordPress Language settings have been changed: ', 'multilingualpress');
            foreach ($languagesForNotices as $siteId => $language) {
                $langs .= "{$language['wpLanguage']} => {$language['mlpLanguage']}, Sited ID: {$siteId}";
            }
            $langMessage .= $langs;
        }

        $title = __('We have changed the functionality of the MultilingualPress language setting', 'multilingualpress');

        try {
            $this->enqueueAssets();
        } catch (AssetException $exc) {
            if (isWpDebugMode()) {
                throw $exc;
            }
        }

        AdminNotice::info($generalMessage . ($langMessage ?? ''))
            ->withTitle($title)
            ->makeDismissible()
            ->inNetworkScreens()
            ->render();
    }
}
