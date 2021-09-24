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
 * Content relations table.
 */
final class ContentRelationsTable implements Table
{
    use TableTrait;

    const COLUMN_CONTENT_ID = 'content_id';
    const COLUMN_RELATIONSHIP_ID = 'relationship_id';
    const COLUMN_SITE_ID = 'site_id';

    /**
     * @var string
     */
    private $prefix;

    /**
     * @param string $tablePrefix
     */
    public function __construct(string $tablePrefix = '')
    {
        $this->prefix = $tablePrefix;
    }

    /**
     * @inheritdoc
     */
    public function columnsWithoutDefaultContent(): array
    {
        return [];
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
            'KEY site_content (%1$s,%2$s)',
            self::COLUMN_SITE_ID,
            self::COLUMN_CONTENT_ID
        );
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return "{$this->prefix}mlp_content_relations";
    }

    /**
     * @inheritdoc
     */
    public function primaryKey(): string
    {
        return sprintf(
            '%1$s,%2$s,%3$s',
            self::COLUMN_RELATIONSHIP_ID,
            self::COLUMN_SITE_ID,
            self::COLUMN_CONTENT_ID
        );
    }

    /**
     * @inheritdoc
     */
    public function schema(): array
    {
        return [
            self::COLUMN_RELATIONSHIP_ID => 'bigint(20) unsigned NOT NULL auto_increment',
            self::COLUMN_SITE_ID => 'bigint(20) NOT NULL',
            self::COLUMN_CONTENT_ID => 'bigint(20) NOT NULL',
        ];
    }
}
