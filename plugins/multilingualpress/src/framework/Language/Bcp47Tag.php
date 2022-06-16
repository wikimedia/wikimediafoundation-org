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

namespace Inpsyde\MultilingualPress\Framework\Language;

use Inpsyde\MultilingualPress\Framework\Stringable;
use InvalidArgumentException;

/**
 * Class Bcp47Tag
 * @package Inpsyde\MultilingualPress\Module\QuickLinks\Model
 */
class Bcp47Tag implements Stringable
{
    use Bcp47tagValidator;

    /**
     * @var string
     */
    private $value;

    /**
     * Bcp47Tag constructor.
     * @param string $bcp47Tag
     * @throws InvalidArgumentException
     */
    public function __construct(string $bcp47Tag)
    {
        if (!$this->validate($bcp47Tag)) {
            throw new InvalidArgumentException('Invalid Bcp47Tag.');
        }

        $this->value = $bcp47Tag;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
