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

namespace Inpsyde\MultilingualPress\Module\User\TranslationUi;

use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use WP_User;

use function Inpsyde\MultilingualPress\siteNameWithLanguage;
use function Inpsyde\MultilingualPress\assignedLanguageNames;

class MetaboxView
{
    /**
     * @var MetaboxFields
     */
    private $fields;

    /**
     * @param MetaboxFields $fields
     */
    public function __construct(MetaboxFields $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Will render user profile translation settings, which includes
     * The section title for MultilingualPress settings and translatable fields
     * for each site in tabs
     *
     * @param WP_User $user The user which is currently in edit
     * @throws NonexistentTable
     */
    public function render(WP_User $user)
    {
        $userProfileTranslationSectionTitleMarkupFormat = '<h2>%1$s</h2>';
        printf(
            wp_kses_post($userProfileTranslationSectionTitleMarkupFormat),
            esc_html__(
                'MultilingualPress: User Profile Translation Settings',
                'multilingualpress'
            )
        );

        $assignedLanguages = assignedLanguageNames();
        ?>
        <div class="mlp-translation-metabox mlp-user-profile-translation-metabox">
            <ul class="nav-tab-wrapper wp-clearfix">
                <?php
                foreach ($assignedLanguages as $siteId => $language) {
                    $this->renderTabAnchor($siteId);
                }
                ?>
            </ul>
            <?php
            foreach ($assignedLanguages as $siteId => $language) {
                $this->renderTabContent($siteId, $user, new MetaboxFieldsHelper($siteId));
            }
            ?>
        </div>
        <?php
    }

    /**
     * Will render translation metabox tab title. Should be the site name
     *
     * @param int $siteId The site id which name should be rendered as tab title
     * @throws NonexistentTable
     */
    protected function renderTabAnchor(int $siteId)
    {
        $siteName = siteNameWithLanguage($siteId);
        $markupFormat = '<li class="nav-tab" id="tab-anchor-%1$s"><a href="#tab-%1$s">%2$s</a></li>';

        printf(
            wp_kses_post($markupFormat),
            (int)$siteId,
            esc_html($siteName)
        );
    }

    /**
     * Will render translation metabox tab content (translatable options)
     *
     * @param int $siteId The site id
     * @param WP_User $user The user which is currently in edit
     * @param MetaboxFieldsHelper $helper
     */
    protected function renderTabContent(int $siteId, WP_User $user, MetaboxFieldsHelper $helper)
    {
        ?>
        <div class="wp-tab-panel"
             id="tab-<?= esc_attr($siteId) ?>"
             data-tab-id="tab-<?= esc_attr($siteId) ?>"
        >
            <table class="form-table <?= sanitize_html_class('tab-' . esc_attr($siteId)) ?>">
                <tbody>
                <?php
                foreach ($this->fields->allFields() as $field) {
                    $field->render($user->ID, $siteId, $helper);
                }
                ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
