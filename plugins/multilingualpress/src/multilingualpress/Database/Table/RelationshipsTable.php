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
 * Relationships table.
 */
final class RelationshipsTable implements Table
{
    use TableTrait;

    const COLUMN_ID = 'id';
    const COLUMN_TYPE = 'type';

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
        return '';
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return "{$this->prefix}mlp_relationships";
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
            self::COLUMN_ID => 'bigint(20) unsigned NOT NULL auto_increment',
            self::COLUMN_TYPE => 'varchar(20) NOT NULL',
        ];
    }
}
