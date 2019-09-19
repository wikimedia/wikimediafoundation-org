
<?php
if ( ! defined( 'WPINC' ) ) die;
use Digital_Climate_Strike_WP_Admin as DcsAdmin;
?>

<div class="wrap digital_climate_strike-container">
    <h2>Digital #ClimateStrike Widget</h2>
    <p>This plugin allows anyone with a Wordpress site to add the <a href="https://github.com/fightforthefuture/digital-climate-strike" target="_blank">Digital #ClimateStrike widget</a> with just a few clicks. <a href="#digital_climate_strike_options">Skip to the settings below</a> to turn it on!</p>
    <h2>How the widget works</h2>
    <p>When you enable the widget below it will show a footer banner (<a href="https://assets.digitalclimatestrike.net/demo.html" target="_blank">demo</a>) informing visitors that your site is supporting the Global Climate Strike and directs them to also join the strike:</p>
    <img src="<?= plugins_url( $this->plugin_name . '/assets/screenshot-1.png')?>" alt="Digital Strike Banner">
    <p>Then at midnight on September 20th for 24 hours, the banner will expand to be full screen (<a href="https://assets.digitalclimatestrike.net/demo.html?fullPage" target="_blank">demo</a>), showing an unavoidable message that your site is joining the Global #ClimateStrike for the day, directing them to join the Global Climate Strike movement:</p>
    <img src="<?= plugins_url( $this->plugin_name . '/assets/screenshot-2.png')?>" alt="Digital Climate Strike Full Page">
    <p>The widget is designed to appear once per user, per device, per day, but can be configured to display at a different interval.</p>
    <form method="post" name="digital_climate_strike_options" class="digital_climate_strike_options-form" action="options.php" id="digital_climate_strike_options">
        <h2>Digital #ClimateStrike Banner Settings:</h2>
        <p>These options allow you to configure the Digital #climateStrike widget to suit your needs.</p>
        <?php
        $options = get_option($this->plugin_name);
        $show_digital_strike_widget = $this->field_is_set($options, 'show_digital_strike_widget') ? DcsAdmin::ENABLE : DcsAdmin::DISABLE;
        $language = $this->field_is_set($options, 'language') ? esc_attr($options['language']) : 'en';
        $force_full_page_widget = $this->field_is_set($options, 'force_full_page_widget') ? DcsAdmin::ENABLE : DcsAdmin::DISABLE;
        $always_show_widget = $this->field_is_set($options, 'always_show_widget') ? DcsAdmin::ENABLE : DcsAdmin::DISABLE;
        $iframe_host = $this->field_is_set($options, 'iframe_host') ? esc_attr($options['iframe_host']) : DcsAdmin::IFRAME_HOST;
        $cookie_expiration_days = $this->field_is_set($options, 'cookie_expiration_days') ? esc_attr($options['cookie_expiration_days']) : DcsAdmin::COOKIE_EXPIRATION_DAYS;
        $disable_google_analytics = $this->field_is_set($options, 'disable_google_analytics') ? DcsAdmin::ENABLE : DcsAdmin::DISABLE;
        $show_close_button_on_full_page_widget = $this->field_is_set($options, 'show_close_button_on_full_page_widget') ? DcsAdmin::ENABLE : DcsAdmin::DISABLE;

        $footer_display_start_date = $this->field_is_set($options, 'footer_display_start_date') ? esc_attr($options['footer_display_start_date']) : date(DcsAdmin::FOOTER_DISPLAY_DATE);
        $full_page_display_start_date = $this->field_is_set($options, 'full_page_display_start_date') ? esc_attr($options['full_page_display_start_date']) : date(DcsAdmin::FULL_PAGE_DISPLAY_DATE);
        settings_fields($this->plugin_name);
        do_settings_sections($this->plugin_name);
        ?>
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-show_digital_strike_widget">
                <span><?php esc_attr_e('Show the Digital #ClimateStrike Widget:', $this->plugin_name); ?></span>
                <input type="checkbox"
                       id="<?php echo $this->plugin_name; ?>-show_digital_strike_widget"
                       name="<?php echo $this->plugin_name; ?>[show_digital_strike_widget]"
                       value="1"
                    <?php checked( $show_digital_strike_widget, 1 ); ?>
                />
                <p>This will make the Digital #ClimateStrike banner show up on your site.</p>
            </label>
        </fieldset>
        <fieldset>
            <span><?php esc_attr_e( 'Language:', $this->plugin_name ); ?></span>
            <select
                    data-placeholder="Choose a Language..."
                    id="<?php echo $this->plugin_name; ?>-language"
                    name="<?php echo $this->plugin_name; ?>[language]"
            >
                <option value="en" <?= !empty( $language ) && $language == 'en' ? 'selected' : '' ?>>English</option>
                <option value="de" <?= !empty( $language ) && $language == 'de' ? 'selected' : '' ?>>German</option>
                <option value="es" <?= !empty( $language ) && $language == 'es' ? 'selected' : '' ?>>Spanish</option>
                <option value="fr" <?= !empty( $language ) && $language == 'fr' ? 'selected' : '' ?>>French</option>
                <option value="pt" <?= !empty( $language ) && $language == 'pt' ? 'selected' : '' ?>>Portuguese</option>
                <option value="cs" <?= !empty( $language ) && $language == 'cs' ? 'selected' : '' ?>>Czech</option>
                <option value="tr" <?= !empty( $language ) && $language == 'tr' ? 'selected' : '' ?>>Turkish</option>
                <option value="nl" <?= !empty( $language ) && $language == 'nl' ? 'selected' : '' ?>>Dutch</option>
            </select>
            <p>Configure the language you want the banner to show as.</p>
        </fieldset>
        <fieldset>
            <span><?php esc_attr_e( 'Cookie Expiration Days:', $this->plugin_name ); ?></span>
            <input type="number"
                   class="digital_climate_strike-cookie_expiration_days"
                   id="<?php echo $this->plugin_name; ?>-cookie_expiration_days"
                   name="<?php echo $this->plugin_name; ?>[cookie_expiration_days]"
                   value="<?= !empty( $cookie_expiration_days ) ? $cookie_expiration_days : 1; ?>"/>
            <p>If the user closes the banner, we set a cookie so they won't see it again on that device of this number of days.</p>
        </fieldset>
        <fieldset>
            <span><?php esc_attr_e( 'iFrame Host:', $this->plugin_name ); ?></span>
            <input type="url"
                   class="digital_climate_strike-iframe_host"
                   id="<?php echo $this->plugin_name; ?>-iframe_host"
                   name="<?php echo $this->plugin_name; ?>[iframe_host]"
                   value="<?= !empty( $iframe_host ) ? $iframe_host : DcsAdmin::IFRAME_HOST; ?>"/>
            <p>If you would like to self-host the iFrame source code, you can configure the hostname for this here.</p>
        </fieldset>
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-disable_google_analytics">
                <span><?php esc_attr_e('Disable Google Analytics:', $this->plugin_name); ?></span>
                <input type="checkbox"
                       id="<?php echo $this->plugin_name; ?>-disable_google_analytics"
                       name="<?php echo $this->plugin_name; ?>[disable_google_analytics]"
                       value="1"
                    <?php checked( $disable_google_analytics, 1 ); ?>
                />
                <p>Removes Google Analytics tracking from the banner.</p>
            </label>
        </fieldset>
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-always_show_widget">
                <span><?php esc_attr_e('Always Show Widget:', $this->plugin_name); ?></span>
                <input type="checkbox"
                       id="<?php echo $this->plugin_name; ?>-always_show_widget"
                       name="<?php echo $this->plugin_name; ?>[always_show_widget]"
                       value="1"
                    <?php checked( $always_show_widget, 1 ); ?>
                />
                <p>Even if someone has closed the widget, this will make it show. Useful for testing purposes.</p>
            </label>
        </fieldset>
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-force_full_page_widget">
                <span><?php esc_attr_e('Force Full Page Widget:', $this->plugin_name); ?></span>
                <input type="checkbox"
                       id="<?php echo $this->plugin_name; ?>-force_full_page_widget"
                       name="<?php echo $this->plugin_name; ?>[force_full_page_widget]"
                       value="1"
                    <?php checked( $force_full_page_widget, 1 ); ?>
                />
                <p>Enforces the full page widget. Useful for testing.</p>
            </label>
        </fieldset>
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-show_close_button_on_full_page_widget">
                <span><?php esc_attr_e('Show close button on Full Page Widget:', $this->plugin_name); ?></span>
                <input type="checkbox"
                       id="<?php echo $this->plugin_name; ?>-show_close_button_on_full_page_widget"
                       name="<?php echo $this->plugin_name; ?>[show_close_button_on_full_page_widget]"
                       value="1"
                    <?php checked( $show_close_button_on_full_page_widget, 1 ); ?>
                />
                <p>Makes the full page banner closeable, in case your site is unable to do a complete shutdown on September 20. (<a href="<?= DcsAdmin::IFRAME_HOST ?>/demo.html?fullPage&showCloseButton" target="_blank">demo</a>)</p>
            </label>
        </fieldset>
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-footer_display_start_date">
                <span><?php esc_attr_e('Footer display start date:', $this->plugin_name); ?></span>
                <input type="date"
                       id="<?php echo $this->plugin_name; ?>-footer_display_start_date"
                       name="<?php echo $this->plugin_name; ?>[footer_display_start_date]"
                       value="<?= $footer_display_start_date ?>"
                />
                <p>Allows you to set the date when the footer banner should start showing. It defaults to an arbitrary date in the past.</p>
            </label>
        </fieldset>
        <fieldset>
            <label for="<?php echo $this->plugin_name; ?>-full_page_display_start_date">
                <span><?php esc_attr_e('Full page display start date:', $this->plugin_name); ?></span>
                <input type="date"
                       id="<?php echo $this->plugin_name; ?>-full_page_display_start_date"
                       name="<?php echo $this->plugin_name; ?>[full_page_display_start_date]"
                       value="<?= $full_page_display_start_date ?>"
                />
                <p>Allows you to set the date when the full page banner should show.</p>
            </label>
        </fieldset>
        <?php submit_button( __( 'Save all changes', $this->plugin_name ), 'primary','submit', TRUE ); ?>
    </form>
</div>
