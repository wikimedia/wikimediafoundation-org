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

namespace Inpsyde\MultilingualPress\Module\Redirect;

/**
 * Session-based noredirect storage implementation, used when no user is logged or no persistent
 * object cache is in use.
 *
 * phpcs:disable WordPress.VIP.SessionVariableUsage
 * phpcs:disable WordPress.VIP.SessionFunctionsUsage
 */
final class NoRedirectSessionStorage implements NoRedirectStorage
{
    /**
     * @inheritdoc
     */
    public function addLanguage(string $language): bool
    {
        $this->ensureSession();

        $session = (array)($_SESSION[NoRedirectStorage::KEY] ?? []);
        $_SESSION[NoRedirectStorage::KEY] = $session;

        if ($this->hasLanguage($language)) {
            return false;
        }

        $_SESSION[NoRedirectStorage::KEY][] = $language;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function hasLanguage(string $language): bool
    {
        $this->ensureSession();

        if (empty($_SESSION[NoRedirectStorage::KEY])) {
            return false;
        }

        return in_array($language, (array)$_SESSION[NoRedirectStorage::KEY], true);
    }

    /**
     * Ensures a session.
     */
    private function ensureSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
