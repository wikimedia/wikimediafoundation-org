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

namespace Inpsyde\MultilingualPress\Database\Table;

use Inpsyde\MultilingualPress\Framework\Database\Table;

/**
 * Site relations table.
 */
final class SiteRelationsTable implements Table
{
    use TableTrait;

    const COLUMN_ID = 'ID';
    const COLUMN_SITE_1 = 'site_1';
    const COLUMN_SITE_2 = 'site_2';

    /**
     * @var string
     */
    private $prefix;

    /**
     * @param string $prefix
     */
    public function __construct(string $prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * @inheritdoc
     */
    public function columnsWithoutDefaultContent(): array
    {
        return [
            self::COLUMN_ID,
        ];
    }

    /**
     * @inheritdoc
     */
    public function defaultContentSql(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function keysSql(): string
    {
        // Due to dbDelta: KEY (not INDEX), and no spaces inside brackets!
        return sprintf(
            'UNIQUE KEY site_combinations (%1$s,%2$s)',
            self::COLUMN_SITE_1,
            self::COLUMN_SITE_2
        );
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return "{$this->prefix}mlp_site_relations";
    }

    /**
     * @inheritdoc
     */
    public function primaryKey(): string
    {
        return self::COLUMN_ID;
    }

    /**
     * @inheritdoc
     */
    public function schema(): array
    {
        return [
            self::COLUMN_ID => 'int unsigned NOT NULL AUTO_INCREMENT',
            self::COLUMN_SITE_1 => 'bigint(20) NOT NULL',
            self::COLUMN_SITE_2 => 'bigint(20) NOT NULL',
        ];
    }
}
