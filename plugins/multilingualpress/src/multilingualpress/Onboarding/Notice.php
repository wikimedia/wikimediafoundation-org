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
 * Onboarding messages
 */
class Notice
{
    /**
     * @var State
     */
    private $onboardingState;

    /**
     * @param State $onboardingState
     */
    public function __construct(State $onboardingState)
    {
        $this->onboardingState = $onboardingState;
    }

    /**
     * Creates onboarding message content.
     *
     * @param string $onboardingState
     * @return \stdClass
     */
    public function onboardingMessageContent(string $onboardingState): \stdClass
    {
        switch ($onboardingState) {
            case State::STATE_SITES:
                return (object)(count(get_sites()) > 1
                    ? $this->forMoreThanOneSite()
                    : $this->forSingleSite()
                );
            case State::STATE_SETTINGS:
                return (object)$this->forSettings();
            case State::STATE_POST:
                return (object)$this->forPosts();
            case State::STATE_END:
                return (object)$this->end();
            default:
                return (object)['title' => '', 'message' => ''];
        }
    }

    /**
     * @param string $message
     * @param string $buttonText
     * @param string $buttonLink
     * @return string
     */
    private function appendButtonToMessage(
        string $message,
        string $buttonText,
        string $buttonLink
    ): string {

        if ($buttonText and $buttonLink) {
            $message .= sprintf(
                '<br/><a class="button button-primary" href="%1$s">%2$s</a>',
                esc_url($buttonLink),
                $buttonText
            );
        }

        return $message;
    }

    /**
     * @return array
     */
    private function forSingleSite(): array
    {
        if (Screen::isNetworkNewSite()) {
            return $this->nullNoticedata();
        }

        $title = _x('Welcome to MultilingualPress!', 'onboarding', 'multilingualpress');
        $buttonText = _x('Create a new Site', 'onboarding', 'multilingualpress');
        $buttonLink = network_admin_url('site-new.php');

        $message = $this->appendButtonToMessage(
            _x(
                'This guide will help you with the setup. <br />At the moment you have only one site. So the first step is to create an additional site and configure the site relationships.',
                'onboarding',
                'multilingualpress'
            ),
            $buttonText,
            $buttonLink
        );

        return [
            'title' => $title,
            'message' => $message,
        ];
    }

    /**
     * @return array
     */
    public function forMoreThanOneSite(): array
    {
        if (Screen::isEditSite()) {
            return $this->nullNoticedata();
        }

        $title = _x('Welcome to MultilingualPress!', 'onboarding', 'multilingualpress');
        $buttonText = _x('Connect Sites', 'onboarding', 'multilingualpress');
        $buttonLink = add_query_arg(
            'id',
            get_network()->site_id,
            network_admin_url('sites.php?page=multilingualpress-site-settings&id=' . get_main_site_id())
        );
        $message = $this->appendButtonToMessage(
            _x(
                'This guide will help you setup site relationships. To get started click the following button:',
                'onboarding',
                'multilingualpress'
            ),
            $buttonText,
            $buttonLink
        );

        return [
            'title' => $title,
            'message' => $message,
        ];
    }

    /**
     * @return array
     */
    private function forSettings(): array
    {
        if (Screen::isMultilingualPressSettings()) {
            return $this->forMultilingualPressSettings();
        }

        $title = __('Configure MultilingualPress Settings', 'multilingualpress');
        $message = $this->appendButtonToMessage(
            _x(
                'The next step is to configure MultilingualPress settings. <br/>You can enable Modules to add more functionality and select the post types and taxonomies that you want to translate.',
                'onboarding',
                'multilingualpress'
            ),
            _x('Go to MultilingualPress Settings', 'onboarding', 'multilingualpress'),
            network_admin_url('admin.php?page=multilingualpress')
        );

        return [
            'title' => $title,
            'message' => $message,
        ];
    }

    /**
     * @return array
     */
    private function forPosts(): array
    {
        if (Screen::isNetworkSite() || Screen::isEditPostsTable()) {
            return $this->nullNoticedata();
        }
        if (Screen::isMultilingualPressSettings()) {
            return $this->forMultilingualPressSettings();
        }

        $title = _x('Connect WordPress Content', 'onboarding', 'multilingualpress');

        $message = _x(
            'You made it! Finally, you can translate and connect content in the edit panel. Now you can go to ',
            'onboarding',
            'multilingualpress'
        );
        $message .= sprintf(
            '<a href="%1$s">%2$s</a>',
            esc_url(admin_url('edit.php')),
            _x('Posts', 'onboarding', 'multilingualpress')
        );
        $message .= __(' or ', 'multilingualpress');
        $message .= sprintf(
            '<a href="%1$s">%2$s</a>%3$s',
            esc_url(admin_url('edit.php?post_type=page')),
            _x('Pages', 'onboarding', 'multilingualpress'),
            _x(' to connect them.', 'onboarding', 'multilingualpress')
        );

        return [
            'title' => $title,
            'message' => $message,
        ];
    }

    /**
     * @return array
     */
    private function forMultilingualPressSettings(): array
    {
        $title = __('Configure MultilingualPress Settings', 'multilingualpress');

        $message = _x(
            'Please enable Modules and select the post types and taxonomies that you want to translate. <br/>Don\'t forget to click "Save Changes". Afterwards you can go to ',
            'onboarding',
            'multilingualpress'
        );
        $message .= sprintf(
            '<a href="%1$s">%2$s</a>',
            esc_url(admin_url('edit.php')),
            _x('Posts', 'onboarding', 'multilingualpress')
        );
        $message .= __(' or ', 'multilingualpress');
        $message .= sprintf(
            '<a href="%1$s">%2$s</a>%3$s',
            esc_url(admin_url('edit.php?post_type=page')),
            _x('Pages', 'onboarding', 'multilingualpress'),
            _x(' to connect them.', 'onboarding', 'multilingualpress')
        );

        return [
            'title' => $title,
            'message' => $message,
        ];
    }

    /**
     * @return array
     */
    private function forEditPostScreen(): array
    {
        $title = _x('Connect WordPress Content', 'onboarding', 'multilingualpress');
        $buttonText = _x('Finish Guide', 'onboarding', 'multilingualpress');
        $buttonLink = add_query_arg(
            Onboarding::OPTION_ONBOARDING_DISMISSED,
            true,
            admin_url('edit.php')
        );

        $message = _x(
            'You made it! Finally, you can translate and connect content in the edit panel. If you need further information ',
            'onboarding',
            'multilingualpress'
        );
        // phpcs:ignore Inpsyde.CodeQuality.LineLength.TooLong
        $message .= sprintf(
            '<a href="%1$s" target="_blank">%2$s</a>',
            'https://multilingualpress.org/docs-category/multilingualpress-3/',
            _x('check out our documentation.', 'onboarding', 'multilingualpress')
        );
        $message = $this->appendButtonToMessage(
            $message,
            $buttonText,
            $buttonLink
        );

        return [
            'title' => $title,
            'message' => $message,
        ];
    }

    /**
     * @return array
     */
    private function end(): array
    {
        if (Screen::isNetworkSite()) {
            return $this->nullNoticedata();
        }
        if (Screen::isEditPostsTable() || Screen::isEditPost()) {
            return $this->forEditPostScreen();
        }

        $title = _x('Connect WordPress Content', 'onboarding', 'multilingualpress');

        $message = _x(
            'You made it! Finally, you can translate and connect content in the edit panel. Now you can go to ',
            'onboarding',
            'multilingualpress'
        );
        $message .= sprintf(
            '<a href="%1$s">%2$s</a>%3$s',
            esc_url(admin_url('edit.php')),
            _x('Posts', 'onboarding', 'multilingualpress'),
            _x(' or ', 'onboarding', 'multilingualpress')
        );
        $message .= sprintf(
            '<a href="%1$s">%2$s</a>%3$s',
            esc_url(admin_url('edit.php?post_type=page')),
            _x('Pages', 'onboarding', 'multilingualpress'),
            _x(' to connect them.', 'onboarding', 'multilingualpress')
        );

        return [
            'title' => $title,
            'message' => $message,
        ];
    }

    /**
     * @return array
     */
    private function nullNoticedata(): array
    {
        return [
            'title' => '',
            'message' => '',
        ];
    }
}
