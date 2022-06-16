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

namespace Inpsyde\MultilingualPress\Core\Admin;

use Inpsyde\MultilingualPress\Framework\Api\SiteRelations;
use Inpsyde\MultilingualPress\Framework\Setting\Site\SiteSettingViewModel;

use function Inpsyde\MultilingualPress\siteNameWithLanguage;

/**
 * MultilingualPress "Relationships" site setting.
 */
final class RelationshipsSiteSetting implements SiteSettingViewModel
{
    /**
     * @var string
     */
    private $id = 'mlp-site-relations';

    /**
     * @var SiteSettingsRepository
     */
    private $settings;

    /**
     * @var SiteRelations
     */
    private $siteRelations;

    /**
     * @param SiteSettingsRepository $settings
     * @param SiteRelations $siteRelations
     */
    public function __construct(SiteSettingsRepository $settings, SiteRelations $siteRelations)
    {
        $this->settings = $settings;
        $this->siteRelations = $siteRelations;
    }

    /**
     * @inheritdoc
     */
    public function render(int $siteId)
    {
        $relatedIds = $this->settings->allSiteIds([$siteId]);
        if (!$relatedIds) {
            ?>
            <p class="description">
                <?php
                esc_html_e(
                    'There are no sites with an assigned language.',
                    'multilingualpress'
                );
                print '<br>';
                esc_html_e(
                    'Please assign a language to other sites to be able to connect them here.',
                    'multilingualpress'
                );
                ?>
            </p>
            <?php
            return;
        }
        ?>
        <p class="description">
            <?php
            esc_html_e(
                'You can connect this site only to sites with an assigned language. Other sites will not show up here.',
                'multilingualpress'
            );
            ?>
        </p>
        <?php
        $this->renderRelationshipsBulkActions();
        $this->renderRelationships($siteId, $relatedIds);
    }

    /**
     * @inheritdoc
     */
    public function title(): string
    {
        return sprintf(
            '<label for="%2$s">%1$s</label>',
            esc_html__('Relationships', 'multilingualpress'),
            esc_attr($this->id)
        );
    }

    /**
     * Render the relationships bulk actions.
     *
     * @return void
     */
    private function renderRelationshipsBulkActions()
    {
        ?>
        <p class="mlp-relationships-bulk-selection">
            <a class="mlp-site-bulk-relations" data-action="select" href="#">
                <?php esc_html_e('Select All', 'multilingualpress'); ?>
            </a>
            /
            <a class="mlp-site-bulk-relations" data-action="deselect" href="#">
                <?php esc_html_e('Deselect All', 'multilingualpress'); ?>
            </a>
        </p>
        <?php
    }

    /**
     * Renders the relationships.
     *
     * @param int $baseSiteId
     * @param array $relatedIds
     * @return void
     */
    private function renderRelationships(int $baseSiteId, array $relatedIds)
    {
        $fieldName = esc_attr(SiteSettingsRepository::NAME_RELATIONSHIPS);
        ?>

        <div class="mlp-relationships-languages">
            <?php
            foreach ($relatedIds as $siteId) :
                $relatedSiteIds = $this->siteRelations->relatedSiteIds((int)$siteId);
                $id = "{$this->id}-{$siteId}";
                ?>
                <p>
                    <label for="<?= esc_attr($id) ?>">
                        <input
                            type="checkbox"
                            name="<?= $fieldName; // phpcs:ignore        ?>[]"
                            value="<?= esc_attr((string)$siteId) ?>"
                            id="<?= esc_attr($id) ?>"
                            <?php checked(in_array($baseSiteId, $relatedSiteIds, true)) ?>>
                        <?= esc_html(siteNameWithLanguage((int)$siteId)) ?>
                    </label>
                </p>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
