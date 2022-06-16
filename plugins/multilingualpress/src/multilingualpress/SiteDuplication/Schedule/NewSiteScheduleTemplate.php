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

namespace Inpsyde\MultilingualPress\SiteDuplication\Schedule;

/**
 * Class NewSiteScheduleTemplate
 * @package Inpsyde\MultilingualPress\SiteDuplication
 */
class NewSiteScheduleTemplate
{
    const ALLOWED_SCREEN_ID = 'site-new-network';

    /**
     * Render the template for the attachment schedule cron jobs
     * Used in the context of a new site to show information about the current status of the
     * attachment copy to the target site.
     *
     * @wp-hook admin_footer
     *
     * @return void
     */
    public function render()
    {
        $allowedHtml = ['span' => ['class' => true]];

        if (!$this->isNewSitePage()) {
            return;
        }
        ?>
        <script type="text/template" id="mlp_new_site_schedule_tmpl">
            <div class="mlp-attachment-schedule-notice notice notice-info">
                <div class="mlp-attachment-schedule-notice__content">
                    <p class="mlp-new-site-schedule-steps">
                        <?php
                        printf(// translators: 1 is the copied files, 2 the total files and 3 the remaining time
                            esc_html_x(
                                'Copying %1$s attachments into new site. Estimated time until finish: %2$s',
                                'Site Attachment Duplication Notice',
                                'multilingualpress'
                            ),
                            wp_kses($this->totalAttachmentsPart(), $allowedHtml),
                            wp_kses($this->scheduleStepsTimeRemainingPart(), $allowedHtml)
                        );
                        ?>
                    </p>
                    <div class="mlp-new-site-schedule-notice-progress">
                        <p class="mlp-new-site-schedule-notice-progress__bar" style="width:0"></p>
                    </div>
                </div>
                <div class="mlp-attachment-schedule-notice__actions">
                    <button class="button button-primary"
                            name="mlp_kill_attachment_duplication"
                            id="mlp_kill_attachment_duplication">
                        <?php esc_html_e('Stop Process', 'multilingualpress') ?>
                    </button>
                </div>
            </div>
        </script>
        <?php
    }

    /**
     * Check against the current page. Ensuring it is the new site admin page
     *
     * @return bool
     */
    private function isNewSitePage(): bool
    {
        $screen = get_current_screen();

        if (!$screen) {
            return false;
        }

        return $screen->id === self::ALLOWED_SCREEN_ID;
    }

    /**
     * @return string
     */
    private function totalAttachmentsPart(): string
    {
        return sprintf(
            '<span class="mlp-new-site-schedule-steps__total-attachments">%s</span>',
            esc_html_x('Unknown', 'Site Attachment Duplication Notice', 'multilingualpress')
        );
    }

    /**
     * @return string
     */
    private function scheduleStepsTimeRemainingPart(): string
    {
        return sprintf(
            '<span class="mlp-new-site-schedule-steps__estimated-remaining-time">%s</span>',
            esc_html_x('Unknown', 'Site Attachment Duplication Notice', 'multilingualpress')
        );
    }
}
