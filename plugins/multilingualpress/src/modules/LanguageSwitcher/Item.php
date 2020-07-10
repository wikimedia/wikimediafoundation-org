<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the MultilingualPress package.
 *
 * (c) Inpsyde GmbH
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Inpsyde\MultilingualPress\Module\LanguageSwitcher;

class Item
{
    /**
     * @var string
     */
    private $isoName;

    /**
     * @var string
     */
    private $isoCode;

    /**
     * @var string
     */
    private $flag;

    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $siteId;

    /**
     * @param string $isoName
     * @param string $isoCode
     * @param string $flag
     * @param string $url
     * @param int $siteId
     */
    public function __construct(
        string $isoName,
        string $isoCode,
        string $flag,
        string $url,
        int $siteId
    ) {

        $this->isoName = $isoName;
        $this->isoCode = $isoCode;
        $this->flag = $flag;
        $this->url = $url;
        $this->siteId = $siteId;
    }

    /**
     * @return string
     */
    public function isoName(): string
    {
        return $this->isoName;
    }

    /**
     * @return string
     */
    public function isoCode(): string
    {
        return $this->isoCode;
    }

    /**
     * @return string
     */
    public function flag(): string
    {
        return $this->flag;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * @return int
     */
    public function siteId(): int
    {
        return $this->siteId;
    }
}
