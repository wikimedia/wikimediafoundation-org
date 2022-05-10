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

namespace Inpsyde\MultilingualPress\SiteDuplication\Settings;

use Inpsyde\MultilingualPress\Framework\Nonce\Nonce;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel;
use Inpsyde\MultilingualPress\SiteDuplication\SiteDuplicator;

use function Inpsyde\MultilingualPress\printNonceField;

/**
 * Site duplication "Based on site" setting.
 */
final class BasedOnSiteSetting implements SiteSettingViewModel
{
    /**
     * @var \wpdb
     */
    private $wpdb;

    /**
     * @var string
     */
    private $fieldId = 'mlp-base-site-id';

    /**
     * @var Nonce
     */
    private $nonce;

    /**
     * @param \wpdb $db
     * @param Nonce $nonce
     */
    public function __construct(\wpdb $db, Nonce $nonce)
    {
        $this->wpdb = $db;
        $this->nonce = $nonce;
    }

    /**
     * @inheritdoc
     */
    public function render(int $siteId)
    {
        ?>
        <select
            id="<?= esc_attr($this->fieldId) ?>"
            name="<?= esc_attr(SiteDuplicator::NAME_BASED_ON_SITE) ?>"
            autocomplete="off">
            <?php $this->renderSelectFieldOptions() ?>
        </select>
        <?php
        printNonceField($this->nonce);
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return sprintf(
            '<label for="%2$s">%1$s</label>',
            esc_html__('Based on site', 'multilingualpress'),
            esc_attr($this->fieldId)
        );
    }

    /**
     * Renders the option tags.
     */
    private function renderSelectFieldOptions()
    {
        ?>
        <option value="0">
            <?php esc_html_e('Choose site', 'multilingualpress') ?>
        </option>
        <?php
        foreach ($this->activeSites() as $site) {
            $path = $site['path'] === '/' ? '' : $site['path'];
            ?>
            <option value="<?= esc_attr($site['id']) ?>">
                <?= esc_url($site['domain'] . $path) ?>
            </option>
            <?php
        }
    }

    /**
     * Returns all existing sites.
     *
     * @return string[][]
     */
    private function activeSites(): array
    {
        //phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
        //phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return (array)$this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT blog_id AS id, domain, path FROM {$this->wpdb->blogs} WHERE deleted = 0 AND site_id = %s",
                $this->wpdb->siteid
            ),
            ARRAY_A
        );
    }
}
