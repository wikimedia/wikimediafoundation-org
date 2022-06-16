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

namespace Inpsyde\MultilingualPress\Module\LanguageManager;

use Inpsyde\MultilingualPress\Core\Admin\LanguagesAjaxSearch;
use Inpsyde\MultilingualPress\Database\Table\LanguagesTable;
use Inpsyde\MultilingualPress\Framework\Language\Language;

use function Inpsyde\MultilingualPress\arrayToAttrs;

/**
 * Language Manager Table Form View
 */
class TableFormView
{
    /**
     * @var string
     */
    private static $languageInstallationStatus = 'language_installation_status';

    /**
     * @var string
     */
    private $name = 'languages';

    /**
     * @var string
     */
    private $id = 'mlp-language-manager-table';

    /**
     * @var Db
     */
    private $db;

    /**
     * @var LanguageInstaller
     */
    private $languageInstaller;

    /**
     * TableFormView constructor.
     * @param Db $db
     */
    public function __construct(Db $db, LanguageInstaller $languageInstaller)
    {
        $this->db = $db;
        $this->languageInstaller = $languageInstaller;
    }

    /**
     * @return void
     */
    public function render()
    {
        $description = __(
            'Click on the "New Language" button below and start typing the language or the country name to search for an already existing language and create a new language based on the existing one.',
            'multilingualpress'
        );
        ?>
        <p class="description">
            <?= esc_html($description) ?>
        </p>
        <table id="<?php echo esc_attr($this->id); ?>"
               class="widefat <?php echo esc_attr($this->id); ?>"
        />
        <thead>
        <tr><?php $this->header(); ?></tr>
        </thead>
        <tbody><?php $this->tBody(); ?></tbody>
        <tfoot>
        <tr><?php $this->header(); ?></tr>
        </tfoot>
        </table>
        <?php
    }

    /**
     * @return void
     */
    private function tBody()
    {
        $rows = $this->db->read(1);

        if (!$rows) {
            $this->emptyRow();
            return;
        }

        /**
         * @var $row Language
         */
        foreach ($rows as $id => $row) {
            $this->row($row->id(), $row);
        }

        $this->emptyRow();
    }

    /**
     * @return void
     */
    private function emptyRow()
    {
        ?>
        <tr>
            <?php
            foreach ($this->columns() as $col => $data) {
                $this->column($col, $this->db->nextLanguageID(), $data, $row->$col ?? '');
            }
            ?>
        </tr>
        <?php
    }

    /**
     * @param int $id
     * @param Language $row
     */
    private function row(int $id, Language $language)
    {
        $cols = [
            LanguagesTable::COLUMN_NATIVE_NAME => $language->nativeName(),
            LanguagesTable::COLUMN_ENGLISH_NAME => $language->englishName(),
            LanguagesTable::COLUMN_BCP_47_TAG => $language->bcp47tag(),
            LanguagesTable::COLUMN_ISO_639_1_CODE => $language->isoCode(LanguagesTable::COLUMN_ISO_639_1_CODE),
            LanguagesTable::COLUMN_ISO_639_2_CODE => $language->isoCode(LanguagesTable::COLUMN_ISO_639_2_CODE),
            LanguagesTable::COLUMN_ISO_639_3_CODE => $language->isoCode(LanguagesTable::COLUMN_ISO_639_3_CODE),
            LanguagesTable::COLUMN_LOCALE => $language->locale(),
            LanguagesTable::COLUMN_RTL => $language->isRtl(),
            self::$languageInstallationStatus => $this->languageInstallationStatus($language),
        ];
        ?>
        <tr>
            <?php
            foreach ($this->columns() as $col => $data) {
                if (!array_key_exists($col, $cols)) {
                    continue;
                }

                $this->column($col, $id, $data, $cols[$col]);
            }
            ?>
        </tr>
        <?php
    }

    /**
     * @param string $col
     * @param int $id
     * @param array $data
     * @param mixed $content
     */
    // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
    private function column(string $col, int $id, array $data, $content)
    {
        // phpcs:enable
        ?>
        <td data-label="<?= esc_attr($data['header'] ?? '') ?>">
            <?php
            $attrs = $data['attributes'] ?? '';
            $data['type'] = $data['type'] ?? '';
            $func = [$this, $data['type']];

            if (!is_callable($func)) {
                echo wp_kses_post($content);
                return;
            }

            $func($id, $col, $content, $attrs);
            ?>
        </td>
        <?php
    }

    /**
     * @param int $id
     * @param string $col
     * @param $value
     * @param array $attributes
     */
    // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
    private function checkbox(int $id, string $col, $value, array $attributes = [])
    {
        // phpcs:enable
        list($name, $id, $attrs) = $this->prepareInputData($id, $col, $value, $attributes);
        ?>
        <input type="checkbox"
               name="<?= esc_attr($name); ?>"
               id="<?= esc_attr($id) ?>"
               value="1"
            <?= esc_attr($attrs); ?>
            <?= checked(1, $value, false); ?>
        />
        <?php
    }

    /**
     * @param int $id
     * @param string $col
     * @param $value
     * @param array $attributes
     */
    // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
    private function number(int $id, string $col, $value, array $attributes = [])
    {
        // phpcs:enable
        list($name, $id, $attrs, $value) = $this->prepareInputData($id, $col, $value, $attributes);
        ?>
        <input type="number"
               name="<?= esc_attr($name); ?>"
               id="<?= esc_attr($id) ?>"
               value="<?= esc_attr($value); ?>"
            <?= esc_attr($attrs); ?>
        />
        <?php
    }

    /**
     * @param int $id
     * @param string $col
     * @param $value
     * @param array $attributes
     */
    // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
    private function text(int $id, string $col, $value, array $attributes = [])
    {
        list($name, $id, $attrs, $value) = $this->prepareInputData($id, $col, $value, $attributes);
        // phpcs:enable
        ?>
        <input type="text"
               name="<?= esc_attr($name); ?>"
               id="<?= esc_attr($id) ?>"
               value="<?= esc_attr($value); ?>"
            <?= esc_attr($attrs); ?>
        />
        <?php
    }

    /**
     * @param int $id
     * @param string $col
     * @param $value
     * @param $attributes
     * @return array
     */
    // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
    private function prepareInputData(int $id, string $col, $value, $attributes): array
    {
        // phpcs:enable
        return [
            $this->inputName($id, $col),
            $this->inputId($id, $col),
            arrayToAttrs($attributes),
            $value,
        ];
    }

    /**
     * @param int $id
     * @param string $col
     * @return string
     */
    private function inputName(int $id, string $col): string
    {
        return $this->name . '[' . $id . '][' . $col . ']';
    }

    /**
     * @param int $id
     * @param string $col
     * @return string
     */
    private function inputId(int $id, string $col): string
    {
        return "{$this->name}-{$id}-${col}";
    }

    /**
     * @return void
     */
    private function header()
    {
        foreach ($this->columns() as $data) {
            $data['header'] = $data['header'] ?? '';
            if (!$data['header']) {
                return;
            }
            // phpcs:disable Inpsyde.CodeQuality.FunctionLength.TooLong
            ?>
            <th scope="col" data-label="<?= esc_attr($data['header'] ?? '') ?>">
                <?= esc_html($data['header']) ?>
            </th>
            <?php
            // phpcs:enable
        }
    }

    /**
     * @return array
     */
    // phpcs:ignore Inpsyde.CodeQuality.FunctionLength.TooLong
    private function columns(): array
    {
        // phpcs:enable

        return [
            LanguagesTable::COLUMN_NATIVE_NAME => [
                'header' => esc_html__('Native name', 'multilingualpress'),
                'type' => 'text',
                'attributes' => [
                    'class' => self::sanitizeColumnsHtmlClass(LanguagesTable::COLUMN_NATIVE_NAME),
                    'size' => 20,
                    'data-connected' => '#' . esc_attr($this->id) . '-tag',
                    'data-action' => esc_attr(LanguagesAjaxSearch::ACTION),
                    'data-none' => __('None', 'multilingualpress'),
                ],
            ],
            LanguagesTable::COLUMN_ENGLISH_NAME => [
                'header' => esc_html__('English name', 'multilingualpress'),
                'type' => 'text',
                'attributes' => [
                    'class' => self::sanitizeColumnsHtmlClass(LanguagesTable::COLUMN_ENGLISH_NAME),
                    'size' => 20,
                ],
            ],
            LanguagesTable::COLUMN_BCP_47_TAG => [
                'header' => esc_html__('HTTP', 'multilingualpress'),
                'type' => 'text',
                'attributes' => [
                    'class' => self::sanitizeColumnsHtmlClass(LanguagesTable::COLUMN_BCP_47_TAG),
                    'size' => 5,
                ],
            ],
            LanguagesTable::COLUMN_ISO_639_1_CODE => [
                'header' => esc_html__('ISO 639-1', 'multilingualpress'),
                'type' => 'text',
                'attributes' => [
                    'class' => self::sanitizeColumnsHtmlClass(LanguagesTable::COLUMN_ISO_639_1_CODE),
                    'size' => 5,
                ],
            ],
            LanguagesTable::COLUMN_ISO_639_2_CODE => [
                'header' => esc_html__('ISO 639-2', 'multilingualpress'),
                'type' => 'text',
                'attributes' => [
                    'class' => self::sanitizeColumnsHtmlClass(LanguagesTable::COLUMN_ISO_639_2_CODE),
                    'size' => 5,
                ],
            ],
            LanguagesTable::COLUMN_ISO_639_3_CODE => [
                'header' => esc_html__('ISO 639-3', 'multilingualpress'),
                'type' => 'text',
                'attributes' => [
                    'class' => self::sanitizeColumnsHtmlClass(LanguagesTable::COLUMN_ISO_639_3_CODE),
                    'size' => 5,
                ],
            ],
            LanguagesTable::COLUMN_LOCALE => [
                'header' => esc_html__('locale', 'multilingualpress'),
                'type' => 'text',
                'attributes' => [
                    'class' => self::sanitizeColumnsHtmlClass(LanguagesTable::COLUMN_LOCALE),
                    'size' => 5,
                ],
            ],
            LanguagesTable::COLUMN_RTL => [
                'header' => esc_html__('RTL', 'multilingualpress'),
                'type' => 'checkbox',
                'attributes' => [
                    'class' => self::sanitizeColumnsHtmlClass(LanguagesTable::COLUMN_RTL),
                ],
            ],
            self::$languageInstallationStatus => [
                'header' => esc_html__('Installed', 'multilingualpress'),
                'attributes' => [
                    'class' => 'installation-status',
                ],
            ],
        ];
    }

    /**
     * @param Language $language
     * @return string
     */
    private function languageInstallationStatus(Language $language): string
    {
        return $this->languageInstaller->exists($language)
            ? sprintf(
                '<p class="language-installed"><span class="screen-reader-text">%s</span></p>',
                esc_html_x('Installed', 'language-manager', 'multilingualpress')
            )
            : sprintf(
                '<p class="language-not-installed"><span class="screen-reader-text">%s</span></p>',
                esc_html_x('Not Installed', 'language-manager', 'multilingualpress')
            );
    }

    /**
     * @param string $class
     * @return string
     */
    private function sanitizeColumnsHtmlClass(string $class): string
    {
        return str_replace('_', '-', sanitize_html_class($class));
    }
}
