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
 * Languages table.
 */
final class ExternalSitesTable implements Table
{
    use TableTrait;

    public const COLUMN_SITE_URL = 'site_url';
    public const COLUMN_SITE_LANGUAGE_NAME = 'site_language_name';
    public const COLUMN_COLUMN_SITE_LANGUAGE_LOCALE = 'site_language_locale';
    public const COLUMN_ID = 'ID';

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
            self::COLUMN_SITE_URL,
            self::COLUMN_SITE_LANGUAGE_NAME,
            self::COLUMN_COLUMN_SITE_LANGUAGE_LOCALE,
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
            'KEY ID (%1$s)',
            self::COLUMN_ID
        );
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return "{$this->prefix}mlp_external_sites";
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
            self::COLUMN_SITE_URL => 'tinytext',
            self::COLUMN_SITE_LANGUAGE_NAME => 'tinytext',
            self::COLUMN_COLUMN_SITE_LANGUAGE_LOCALE => 'varchar(20)',
        ];
    }
}
