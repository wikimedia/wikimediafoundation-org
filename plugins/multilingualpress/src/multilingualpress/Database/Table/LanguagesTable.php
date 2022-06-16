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
final class LanguagesTable implements Table
{
    use TableTrait;

    const COLUMN_CUSTOM_NAME = 'custom_name';
    const COLUMN_ENGLISH_NAME = 'english_name';
    const COLUMN_BCP_47_TAG = 'http_code';
    const COLUMN_ID = 'ID';
    const COLUMN_ISO_639_1_CODE = 'iso_639_1';
    const COLUMN_ISO_639_2_CODE = 'iso_639_2';
    const COLUMN_ISO_639_3_CODE = 'iso_639_3';
    const COLUMN_LOCALE = 'locale';
    const COLUMN_NATIVE_NAME = 'native_name';
    const COLUMN_RTL = 'is_rtl';

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
            self::COLUMN_CUSTOM_NAME,
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
            'KEY http_code (%1$s)',
            self::COLUMN_BCP_47_TAG
        );
    }

    /**
     * @inheritdoc
     */
    public function name(): string
    {
        return "{$this->prefix}mlp_languages";
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
            self::COLUMN_ENGLISH_NAME => 'tinytext',
            self::COLUMN_NATIVE_NAME => 'tinytext',
            self::COLUMN_CUSTOM_NAME => 'tinytext',
            self::COLUMN_ISO_639_1_CODE => 'varchar(8)',
            self::COLUMN_ISO_639_2_CODE => 'varchar(8)',
            self::COLUMN_ISO_639_3_CODE => 'varchar(8)',
            self::COLUMN_LOCALE => 'varchar(20)',
            self::COLUMN_BCP_47_TAG => 'varchar(20)',
            self::COLUMN_RTL => 'tinyint(1) unsigned DEFAULT 0',
        ];
    }
}
