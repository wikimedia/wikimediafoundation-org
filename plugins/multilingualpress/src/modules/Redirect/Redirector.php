<?php

# -*- coding: utf-8 -*-

namespace Inpsyde\MultilingualPress\Module\Redirect;

/**
 * Interface for all redirector implementations.
 */
interface Redirector
{
    const FILTER_REDIRECTOR_TYPE = 'multilingualpress.redirector_type';
    const ACTION_TARGET_NOT_FOUND = 'multilingualpress.redirect_target_not_found';

    const TYPE_JAVASCRIPT = 'JAVASCRIPT';
    const TYPE_PHP = 'PHP';

    /**
     * Redirects the user to the best-matching language version, if any.
     *
     * @return void
     */
    public function redirect();
}
