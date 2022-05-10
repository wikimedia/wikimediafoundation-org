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

/**
 * Model for an admin notice.
 */
class AdminNotice
{
    const DISMISSIBLE = 8192;

    const IN_ALL_SCREENS = 128;
    const IN_NETWORK_SCREENS = 256;
    const IN_USER_SCREENS = 512;
    const IN_DEFAULT_SCREENS = 1024;

    const TYPE_SUCCESS = 1;
    const TYPE_ERROR = 2;
    const TYPE_INFO = 4;
    const TYPE_WARNING = 8;
    const TYPE_MULTILINGUALPRESS = 16;

    const HOOKS = [
        self::IN_ALL_SCREENS => 'all_admin_notices',
        self::IN_DEFAULT_SCREENS => 'admin_notices',
        self::IN_NETWORK_SCREENS => 'network_admin_notices',
        self::IN_USER_SCREENS => 'user_admin_notices',
    ];

    const CLASSES = [
        self::TYPE_ERROR => 'notice-error',
        self::TYPE_WARNING => 'notice-warning',
        self::TYPE_SUCCESS => 'notice-success',
        self::TYPE_INFO => 'notice-info',
        self::TYPE_MULTILINGUALPRESS => 'notice-multilingualpress',
    ];

    const KSES_ALLOWED = [
        'a' => [
            'href' => [],
            'title' => [],
            'class' => [],
            'style' => [],
            'target' => [],
        ],
        'br' => [],
        'em' => [],
        'strong' => [],
        'div' => [
            'style' => [],
            'class' => [],
        ],
    ];

    /**
     * @var string[]
     */
    private $content;

    /**
     * @var int
     */
    private $flags;

    /**
     * @var string
     */
    private $title;

    /**
     * @param string[] $content
     * @return AdminNotice
     */
    public static function error(string ...$content): AdminNotice
    {
        return new static(self::TYPE_ERROR, null, ...$content);
    }

    /**
     * @param string[] $content
     * @return AdminNotice
     */
    public static function info(string ...$content): AdminNotice
    {
        return new static(self::TYPE_INFO, null, ...$content);
    }

    /**
     * @param string[] $content
     * @return AdminNotice
     */
    public static function success(string ...$content): AdminNotice
    {
        return new static(self::TYPE_SUCCESS, null, ...$content);
    }

    /**
     * @param string[] $content
     * @return AdminNotice
     */
    public static function warning(string ...$content): AdminNotice
    {
        return new static(self::TYPE_WARNING, null, ...$content);
    }

    /**
     * @param string[] $content
     * @return AdminNotice
     */
    public static function multilingualpress(string ...$content): AdminNotice
    {
        return new static(self::TYPE_MULTILINGUALPRESS, null, ...$content);
    }

    /**
     * @param int|null $flags
     * @param string|null $title
     * @param string[] $content
     */
    public function __construct(int $flags = null, string $title = null, string ...$content)
    {
        $this->flags = $this->normalizeFlags($flags);
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * @return AdminNotice
     */
    public function makeDismissible(): AdminNotice
    {
        $this->flags |= self::DISMISSIBLE;

        return $this;
    }

    /**
     * @return AdminNotice
     */
    public function inAllScreens(): AdminNotice
    {
        return $this->updateScreen(self::IN_ALL_SCREENS);
    }

    /**
     * @return AdminNotice
     */
    public function inDefaultScreens(): AdminNotice
    {
        return $this->updateScreen(self::IN_DEFAULT_SCREENS);
    }

    /**
     * @return AdminNotice
     */
    public function inNetworkScreens(): AdminNotice
    {
        return $this->updateScreen(self::IN_NETWORK_SCREENS);
    }

    /**
     * @return AdminNotice
     */
    public function inUserScreens(): AdminNotice
    {
        return $this->updateScreen(self::IN_USER_SCREENS);
    }

    /**
     * @param string $title
     * @return AdminNotice
     */
    public function withTitle(string $title): AdminNotice
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return void
     */
    public function render()
    {
        $action = $this->action();

        if (!doing_action($action)) {
            // Either too early or too late. If too early, do again later, if too late do nothing.
            did_action($action) or add_action($action, [$this, 'render']);

            return;
        }

        $this->renderNow();
    }

    /**
     * @return void
     */
    public function renderNow()
    {
        $content = array_filter($this->content);
        if (!$content && !$this->title) {
            return;
        }

        if ($this->title) {
            $title = sprintf(
                '<span class="notice-title">%s</span>',
                wp_strip_all_tags($this->title)
            );
        }

        $classes = $this->sanitizeHtmlClassesByString($this->classes());
        $paragraphs = '<p>' . implode('</p><p>', array_map([$this, 'kses'], $content)) . '</p>';

        // phpcs:disable WordPress.Security
        printf(
            '<div data-action="mlp_action_dismiss" class="%1$s">%2$s%3$s</div>',
            $classes,
            $title ?? '',
            $paragraphs
        );
        // phpcs:enable

        $this->title = '';
        $this->content = [];
    }

    /**
     * @return string
     */
    public function action(): string
    {
        return self::HOOKS[$this->normalizeScreensFlag($this->flags)];
    }

    /**
     * @return string
     */
    private function classes(): string
    {
        $base = $this->isDismissible($this->flags)
            ? 'notice is-dismissible'
            : 'notice';

        return "{$base} " . self::CLASSES[$this->normalizeTypeFlag($this->flags)];
    }

    /**
     * @param int $screenFlag
     * @return AdminNotice
     */
    private function updateScreen(int $screenFlag): AdminNotice
    {
        $base = $this->isDismissible($this->flags) ? self::DISMISSIBLE : 0;
        $typeFlag = $this->normalizeTypeFlag($this->flags);
        $this->flags = $base | $typeFlag | $screenFlag;

        return $this;
    }

    /**
     * @param int|null $flags
     * @return int
     */
    private function normalizeFlags(int $flags = null): int
    {
        if ($flags === null) {
            return self::TYPE_INFO | self::IN_DEFAULT_SCREENS;
        }

        $isDismissiable = $this->isDismissible($flags) ? self::DISMISSIBLE : 0;

        return
            $isDismissiable
            | $this->normalizeTypeFlag($flags)
            | $this->normalizeScreensFlag($flags);
    }

    /**
     * @param int $flags
     * @return int
     */
    private function normalizeTypeFlag(int $flags): int
    {
        switch (true) {
            case (($flags & self::TYPE_ERROR) > 0):
                return self::TYPE_ERROR;
            case (($flags & self::TYPE_WARNING) > 0):
                return self::TYPE_WARNING;
            case (($flags & self::TYPE_SUCCESS) > 0):
                return self::TYPE_SUCCESS;
            case (($flags & self::TYPE_MULTILINGUALPRESS) > 0):
                return self::TYPE_MULTILINGUALPRESS;
        }

        return self::TYPE_INFO;
    }

    /**
     * @param int $flags
     * @return bool
     */
    private function isDismissible(int $flags): bool
    {
        return ($flags & self::DISMISSIBLE) > 0;
    }

    /**
     * @param int $flags
     * @return int
     */
    private function normalizeScreensFlag(int $flags): int
    {
        switch (true) {
            case (($flags & self::IN_NETWORK_SCREENS) > 0):
                return self::IN_NETWORK_SCREENS;
            case (($flags & self::IN_USER_SCREENS) > 0):
                return self::IN_USER_SCREENS;
            case (($flags & self::IN_ALL_SCREENS) > 0):
                return self::IN_ALL_SCREENS;
        }

        return self::IN_DEFAULT_SCREENS;
    }

    /**
     * @param string $message
     * @return string
     */
    private function kses(string $message): string
    {
        return wp_kses($message, self::KSES_ALLOWED, ['http', 'https']);
    }

    /**
     * @param array $classes
     * @return array
     */
    private function sanitizeHtmlClasses(array $classes): array
    {
        return array_map('sanitize_html_class', $classes);
    }

    /**
     * @param string $classes
     * @return string
     */
    private function sanitizeHtmlClassesByString(string $classes): string
    {
        $classes = explode(' ', $classes);
        $classes = $this->sanitizeHtmlClasses($classes);

        if (!$classes) {
            return '';
        }

        $classes = implode(' ', $classes);

        return $classes;
    }
}
