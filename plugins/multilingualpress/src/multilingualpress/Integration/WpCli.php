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

namespace Inpsyde\MultilingualPress\Integration;

use Inpsyde\MultilingualPress\Framework\Integration\Integration;
use Inpsyde\MultilingualPress\Installation\SystemChecker;

use const Inpsyde\MultilingualPress\ACTION_ACTIVATION;

/**
 * WP-CLI integration controller.
 */
final class WpCli implements Integration
{
    /**
     * Integrates WP-CLI.
     *
     * @return bool
     */
    public function integrate(): bool
    {
        if (!defined('WP_CLI') || !WP_CLI) {
            return false;
        }

        if (did_action(ACTION_ACTIVATION)) {
            // Force installation check and thus allow to execute installation or upgrade routines.
            add_filter(SystemChecker::FILTER_FORCE_CHECK, '__return_true');
        }

        return true;
    }
}
