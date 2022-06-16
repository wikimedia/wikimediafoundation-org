<?php

/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress;

const ACTION_ACTIVATION = 'multilingualpress.activation';
const ACTION_ADD_SERVICE_PROVIDERS = 'multilingualpress.add_service_providers';
const ACTION_LOG = 'multilingualpress.log';

const MULTILINGUALPRESS_LICENSE_API_URL = 'https://multilingualpress.org/';

if (!defined('INPUT_REQUEST')) {
    define('INPUT_REQUEST', 99);
}
