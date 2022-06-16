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

namespace Inpsyde\MultilingualPress\Framework\Admin;

class PersistentAdminNotices
{
    const OPTION_NAME = 'multilingualpress_notices_';
    const DEFAULT_TTL = 300;
    const FILTER_ADMIN_NOTICE_TTL = 'multilingualpress.admin_notice_ttl';

    /**
     * @var array
     */
    private $messages = [];

    /**
     * @var bool[]
     */
    private $printed = [];

    /**
     * @var bool
     */
    private $recorded = false;

    /**
     * @return void
     */
    public function init()
    {
        add_action('shutdown', [$this, 'record']);
        add_action('admin_notices', [$this, 'doDefaultNotices']);
        add_action('network_admin_notices', [$this, 'doNetworkNotices']);
        add_action('user_admin_notices', [$this, 'doUserNotices']);
        add_action('all_admin_notices', [$this, 'doAllNotices']);
    }

    /**
     * @param AdminNotice $notice
     * @param string|null $onlyOnScreen
     * @return PersistentAdminNotices
     */
    public function add(AdminNotice $notice, string $onlyOnScreen = null): PersistentAdminNotices
    {
        $userId = get_current_user_id();
        if (!$userId) {
            return $this;
        }

        $action = $notice->action();
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $onlyOnScreen === null and $onlyOnScreen = '*';

        if (!array_key_exists($action, $this->messages)) {
            $this->messages[$action] = [];
        }
        if (!array_key_exists($onlyOnScreen, $this->messages[$action])) {
            $this->messages[$action][$onlyOnScreen] = [];
        }

        $this->messages[$action][$onlyOnScreen][] = [$notice, $now->getTimestamp()];

        return $this;
    }

    /**
     * @wp-hook admin_notices
     */
    public function doDefaultNotices()
    {
        $this->doNotices(AdminNotice::HOOKS[AdminNotice::IN_DEFAULT_SCREENS]);
    }

    /**
     * @wp-hook network_admin_notices
     */
    public function doNetworkNotices()
    {
        $this->doNotices(AdminNotice::HOOKS[AdminNotice::IN_NETWORK_SCREENS]);
    }

    /**
     * @wp-hook user_admin_notices
     */
    public function doUserNotices()
    {
        $this->doNotices(AdminNotice::HOOKS[AdminNotice::IN_USER_SCREENS]);
    }

    /**
     * @wp-hook all_admin_notices
     */
    public function doAllNotices()
    {
        $this->doNotices(AdminNotice::HOOKS[AdminNotice::IN_ALL_SCREENS]);
    }

    /**
     * Store (or delete) messages on shutdown.
     *
     * @return bool
     */
    public function record(): bool
    {
        if ($this->recorded || !doing_action('shutdown')) {
            return false;
        }

        $this->recorded = true;

        $userId = get_current_user_id();
        if ($userId) {
            return $this->messages
                ? (bool)update_user_option($userId, self::OPTION_NAME, $this->messages)
                : (bool)delete_user_option($userId, self::OPTION_NAME);
        }

        return false;
    }

    /**
     * @param string $action
     * @return bool
     */
    private function doNotices(string $action): bool
    {
        if (($this->printed[$action] ?? false) || !doing_action($action)) {
            return false;
        }

        $this->printed[$action] = true;

        $userId = get_current_user_id();
        $screenId = get_current_screen()->id;
        $messages = (array)get_user_option(self::OPTION_NAME, $userId);

        $toPrint = $messages[$action][$screenId] ?? [];
        $toPrint = array_merge($toPrint, $messages[$action]['*'] ?? []);

        unset($messages[$action][$screenId], $messages[$action]['*']);
        if (empty($messages[$action])) {
            unset($messages[$action]);
        }

        if (!empty($toPrint)) {
            $this->printMessages($toPrint);
            return $messages
                ? (bool)update_user_option($userId, self::OPTION_NAME, $messages)
                : (bool)delete_user_option($userId, self::OPTION_NAME);
        }

        return (bool)delete_user_option($userId, self::OPTION_NAME);
    }

    /**
     * @param array $adminNoticesData
     */
    private function printMessages(array $adminNoticesData)
    {
        $now = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->getTimestamp();

        /**
         * Filters if the time-to-live (in seconds) of the admin notice.
         *
         * @param int $timeToLive
         */
        $ttl = apply_filters(self::FILTER_ADMIN_NOTICE_TTL, self::DEFAULT_TTL);
        $expireTime = $now - (int)$ttl;

        /**
         * @var AdminNotice $notice
         * @var int $timestamp
         */
        foreach ($adminNoticesData as list($notice, $timestamp)) {
            if ($expireTime <= $timestamp) {
                $notice->renderNow();
            }
        }
    }
}
