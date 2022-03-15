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

use function Inpsyde\MultilingualPress\combineAtts;

/**
 * @method int contentId()
 * @method string language()
 * @method int priority()
 * @method int siteId()
 * @method string url()
 * @method float userPriority()
 * @method int languageFallbackPriority()
 */
class RedirectTarget
{
    const KEY_CONTENT_ID = 'contentId';

    const KEY_LANGUAGE = 'language';

    const KEY_PRIORITY = 'priority';

    const KEY_SITE_ID = 'siteId';

    const KEY_URL = 'url';

    const KEY_USER_PRIORITY = 'userPriority';

    const KEY_LANGUAGE_FALLBACK_PRIORITY = 'languageFallbackPriority';

    const DEFAULTS = [
        self::KEY_CONTENT_ID => 0,
        self::KEY_LANGUAGE => '',
        self::KEY_PRIORITY => 0,
        self::KEY_SITE_ID => 0,
        self::KEY_URL => '',
        self::KEY_USER_PRIORITY => 0.0,
        self::KEY_LANGUAGE_FALLBACK_PRIORITY => 0,
    ];

    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $data = combineAtts(self::DEFAULTS, $data);

        $data[static::KEY_CONTENT_ID] = (int)$data[static::KEY_CONTENT_ID];
        $data[static::KEY_LANGUAGE] = (string)$data[static::KEY_LANGUAGE];
        $data[static::KEY_PRIORITY] = (int)$data[static::KEY_PRIORITY];
        $data[static::KEY_SITE_ID] = (int)$data[static::KEY_SITE_ID];
        $data[static::KEY_URL] = (string)$data[static::KEY_URL];
        $data[static::KEY_USER_PRIORITY] = (float)$data[static::KEY_USER_PRIORITY];
        $data[static::KEY_LANGUAGE_FALLBACK_PRIORITY] = (int)$data[static::KEY_LANGUAGE_FALLBACK_PRIORITY];

        $this->data = $data;
    }

    /**
     * @param string $name
     * @param array $args
     * @return int|string|float
     *
     * phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration
     */
    public function __call(string $name, array $args = [])
    {
        // phpcs:disable

        if (!array_key_exists($name, self::DEFAULTS)) {
            throw new \Error(sprintf('Call to undefined method %s::%s().', __CLASS__, $name));
        }


        return $this->data[$name];
    }
}
