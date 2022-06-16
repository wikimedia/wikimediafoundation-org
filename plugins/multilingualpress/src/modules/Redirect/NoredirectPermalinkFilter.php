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

use Inpsyde\MultilingualPress\Framework\Api\Translation;
use Inpsyde\MultilingualPress\Framework\Filter\Filter;
use Inpsyde\MultilingualPress\Framework\Filter\FilterTrait;

use function Inpsyde\MultilingualPress\assignedLanguageTags;
use function Inpsyde\MultilingualPress\wpHookProxy;

/**
 * Permalink filter adding the noredirect query argument.
 */
final class NoredirectPermalinkFilter implements Filter
{
    use FilterTrait;

    const QUERY_ARGUMENT = 'noredirect';

    /**
     * @var string[]
     */
    private $languages;

    /**
     * @param int $priority
     */
    public function __construct(int $priority = self::DEFAULT_PRIORITY)
    {
        $this->acceptedArgs = 2;
        $this->callback = wpHookProxy([$this, 'addNoRedirectQueryArgument']);
        $this->hook = Translation::FILTER_URL;
        $this->priority = $priority;
    }

    /**
     * Adds the no-redirect query argument to the permalink, if applicable.
     *
     * @param string $url
     * @param int $siteId
     * @return string
     */
    public function addNoRedirectQueryArgument(string $url, int $siteId): string
    {
        if (!$url) {
            return $url;
        }

        $languages = $this->languages();
        if (empty($languages[$siteId])) {
            return $url;
        }

        return add_query_arg(static::QUERY_ARGUMENT, $languages[$siteId], $url);
    }

    /**
     * Removes the noredirect query argument from the given URL.
     *
     * @param string $url
     * @return string
     */
    public function removeNoRedirectQueryArgument(string $url): string
    {
        return remove_query_arg(self::QUERY_ARGUMENT, $url);
    }

    /**
     * Returns the individual MultilingualPress language code of all (related)
     * sites with site IDs as keys and the individual MultilingualPress language
     * code as values.
     *
     * @return string[]
     */
    private function languages(): array
    {
        if (!is_array($this->languages)) {
            $this->languages = assignedLanguageTags();
        }

        return $this->languages;
    }
}
