<?php
GFForms::include_addon_framework();
 
class hCAPTCHAAddOn extends GFAddOn {
 
    protected $_version = HCAPTCHA_ADDON_VERSION;
    protected $_min_gravityforms_version = '2.5';
    protected $_slug = 'hcaptcha';
    protected $_full_path = __FILE__;
    protected $_short_title = 'hCaptcha';
 
    private static $_instance = null;
 
    public static function get_instance() {
        if ( self::$_instance == null ) {
            self::$_instance = new hCAPTCHAAddOn();
        }
 
        return self::$_instance;
    }

    // Set icon for settings page tab
    public function get_menu_icon() {

        return '<svg height="2500" viewBox="0 0 599.18 599.18" width="2500" xmlns="http://www.w3.org/2000/svg"><path d="m374.48 524.29h74.9v74.89h-74.9z" fill="#0074bf" opacity=".502"/><path d="m299.59 524.29h74.89v74.89h-74.89zm-74.89 0h74.89v74.89h-74.89z" fill="#0074bf" opacity=".702"/><path d="m149.8 524.29h74.9v74.89h-74.9z" fill="#0074bf" opacity=".502"/><path d="m449.39 449.39h74.9v74.9h-74.9z" fill="#0082bf" opacity=".702"/><path d="m374.48 449.39h74.9v74.9h-74.9z" fill="#0082bf" opacity=".8"/><path d="m299.59 449.39h74.89v74.9h-74.89zm-74.89 0h74.89v74.9h-74.89z" fill="#0082bf"/><path d="m149.8 449.39h74.9v74.9h-74.9z" fill="#0082bf" opacity=".8"/><path d="m74.89 449.39h74.9v74.9h-74.9z" fill="#0082bf" opacity=".702"/><g fill="#008fbf"><path d="m524.29 374.48h74.89v74.9h-74.89z" opacity=".502"/><path d="m449.39 374.48h74.9v74.9h-74.9z" opacity=".8"/><path d="m374.48 374.48h74.9v74.9h-74.9zm-74.89 0h74.89v74.9h-74.89zm-74.89 0h74.89v74.9h-74.89z"/><path d="m149.8 374.48h74.9v74.9h-74.9z"/><path d="m74.89 374.48h74.9v74.9h-74.9z" opacity=".8"/><path d="m0 374.48h74.89v74.9h-74.89z" opacity=".502"/></g><path d="m524.29 299.59h74.89v74.89h-74.89z" fill="#009dbf" opacity=".702"/><path d="m449.39 299.59h74.9v74.89h-74.9zm-74.91 0h74.9v74.89h-74.9zm-74.89 0h74.89v74.89h-74.89zm-74.89 0h74.89v74.89h-74.89z" fill="#009dbf"/><path d="m149.8 299.59h74.9v74.89h-74.9zm-74.91 0h74.9v74.89h-74.9z" fill="#009dbf"/><path d="m0 299.59h74.89v74.89h-74.89z" fill="#009dbf" opacity=".702"/><path d="m524.29 224.7h74.89v74.89h-74.89z" fill="#00abbf" opacity=".702"/><path d="m449.39 224.7h74.9v74.89h-74.9zm-74.91 0h74.9v74.89h-74.9zm-74.89 0h74.89v74.89h-74.89zm-74.89 0h74.89v74.89h-74.89z" fill="#00abbf"/><path d="m149.8 224.7h74.9v74.89h-74.9zm-74.91 0h74.9v74.89h-74.9z" fill="#00abbf"/><path d="m0 224.7h74.89v74.89h-74.89z" fill="#00abbf" opacity=".702"/><g fill="#00b9bf"><path d="m524.29 149.8h74.89v74.9h-74.89z" opacity=".502"/><path d="m449.39 149.8h74.9v74.9h-74.9z" opacity=".8"/><path d="m374.48 149.8h74.9v74.9h-74.9zm-74.89 0h74.89v74.9h-74.89zm-74.89 0h74.89v74.9h-74.89z"/><path d="m149.8 149.8h74.9v74.9h-74.9z"/><path d="m74.89 149.8h74.9v74.9h-74.9z" opacity=".8"/><path d="m0 149.8h74.89v74.9h-74.89z" opacity=".502"/></g><path d="m449.39 74.89h74.9v74.9h-74.9z" fill="#00c6bf" opacity=".702"/><path d="m374.48 74.89h74.9v74.9h-74.9z" fill="#00c6bf" opacity=".8"/><path d="m299.59 74.89h74.89v74.9h-74.89zm-74.89 0h74.89v74.9h-74.89z" fill="#00c6bf"/><path d="m149.8 74.89h74.9v74.9h-74.9z" fill="#00c6bf" opacity=".8"/><path d="m74.89 74.89h74.9v74.9h-74.9z" fill="#00c6bf" opacity=".702"/><path d="m374.48 0h74.9v74.89h-74.9z" fill="#00d4bf" opacity=".502"/><path d="m299.59 0h74.89v74.89h-74.89zm-74.89 0h74.89v74.89h-74.89z" fill="#00d4bf" opacity=".702"/><path d="m149.8 0h74.9v74.89h-74.9z" fill="#00d4bf" opacity=".502"/><path d="m197.2 275.96 20.87-46.71c7.61-11.97 6.6-26.64-1.72-34.96-.28-.28-.56-.55-.86-.81-.29-.26-.59-.52-.89-.76a21.043 21.043 0 0 0 -1.92-1.37 22.68 22.68 0 0 0 -4.51-2.13c-1.58-.55-3.21-.92-4.87-1.12-1.66-.19-3.34-.2-5-.03s-3.3.51-4.88 1.04c-1.79.55-3.53 1.27-5.19 2.13a32.32 32.32 0 0 0 -4.72 3.02 32.38 32.38 0 0 0 -4.12 3.82 32 32 0 0 0 -3.37 4.48c-.98 1.59-28.57 66.66-39.2 96.62s-6.39 84.91 34.61 125.99c43.48 43.48 106.43 53.41 146.58 23.28.42-.21.84-.44 1.24-.67.41-.23.81-.48 1.2-.74.4-.25.78-.52 1.16-.8.38-.27.75-.56 1.11-.86l123.73-103.32c6.01-4.97 14.9-15.2 6.92-26.88-7.79-11.39-22.55-3.64-28.57.21l-71.21 51.78c-.33.27-.72.48-1.13.6-.42.12-.85.16-1.28.11s-.85-.19-1.22-.4c-.38-.21-.71-.5-.97-.85-1.81-2.22-2.13-8.11.71-10.44l109.16-92.64c9.43-8.49 10.74-20.84 3.1-29.3-7.45-8.29-19.29-8.04-28.8.53l-98.28 76.83c-.46.38-.99.66-1.56.82s-1.17.21-1.76.13-1.15-.27-1.66-.58c-.51-.3-.96-.7-1.3-1.18-1.94-2.18-2.69-5.89-.5-8.07l111.3-108.01c2.09-1.95 3.78-4.29 4.96-6.88 1.18-2.6 1.85-5.41 1.95-8.26s-.36-5.7-1.36-8.37c-1-2.68-2.51-5.13-4.45-7.22-.97-1.03-2.05-1.95-3.2-2.75a21.14 21.14 0 0 0 -3.69-2.05c-1.3-.55-2.65-.97-4.03-1.26-1.38-.28-2.79-.42-4.2-.41-1.44-.02-2.88.1-4.29.37a21.906 21.906 0 0 0 -7.96 3.16c-1.21.78-2.34 1.68-3.38 2.68l-113.73 106.83c-2.72 2.72-8.04 0-8.69-3.18-.06-.28-.08-.57-.07-.86s.06-.58.15-.85c.08-.28.2-.55.35-.79.15-.25.33-.48.54-.68l87.05-99.12a21.38 21.38 0 0 0 6.82-15.3c.11-5.81-2.15-11.42-6.25-15.53-4.11-4.12-9.71-6.4-15.52-6.31s-11.34 2.53-15.32 6.77l-132.01 145.95c-4.73 4.73-11.7 4.97-15.02 2.22-.51-.4-.93-.9-1.24-1.46-.32-.56-.52-1.18-.6-1.82-.08-.65-.03-1.3.14-1.92s.46-1.21.85-1.72z" fill="#fff"/></svg>';

    }

    // Global plugin settings
    public function plugin_settings_fields() {
        return array(
            array(
                'title'  => esc_html__( "Global hCaptcha settings", 'gf-hcaptcha' ),
                'description' => esc_html__( "Don't have a account?", 'gf-hcaptcha' ) . ' <a href="https://hCaptcha.com/?r=e3d96a76f4c8" target="_blank">' . esc_html__( 'Register', 'gf-hcaptcha' ) . '</a> ' . esc_html__( 'first on the hCaptcha website.', 'gf-hcaptcha' ),
                'fields' => array(
                    array(
                        'name'                  => 'hcaptcha-secretkey',
                        'tooltip'               => '<h6>' . esc_html__( 'Secret key hCaptcha', 'gf-hcaptcha' ) . '</h6>' . esc_html__( 'Please insert your', 'gf-hcaptcha' ) . ' <a href="https://hCaptcha.com/?r=e3d96a76f4c8" target="_blank">' . esc_html__( 'hCaptcha', 'gf-hcaptcha' ) . '</a> ' . esc_html__( 'secret key here. You can find this in your', 'gf-hcaptcha' ) . ' <a href="https://hCaptcha.com/?r=e3d96a76f4c8" target="_blank">' . esc_html__( 'hCaptcha', 'gf-hcaptcha' ) . '</a> ' . esc_html__( 'dashboard under the tab', 'gf-hcaptcha' ) . ' <strong>' . esc_html__( 'settings', 'gf-hcaptcha' ) . '</strong>.',
                        'label'                 => '<label for="hcaptcha-secretkey"><strong>' . esc_html__( 'Secret key', 'gf-hcaptcha' ) . '</strong></label>',
                        'type'                  => 'text',
                        'input_type'            => 'password',
                        'style'                 => 'width:350px;',
                        'required'              => true,
                        'autocomplete'          => 'off'
                    ),
                    array(
                        'name'                  => 'hcaptcha-sitekey',
                        'tooltip'               => '<h6>' . esc_html__( 'Global site key hCaptcha', 'gf-hcaptcha' ) . '</h6>' . esc_html__( 'Please insert your', 'gf-hcaptcha' ) . ' <a href="https://hCaptcha.com/?r=e3d96a76f4c8" target="_blank">' . esc_html__( 'hCaptcha', 'gf-hcaptcha' ) . '</a> ' . esc_html__( 'site key here. You can find this in your', 'gf-hcaptcha' ) . ' <a href="https://hCaptcha.com/?r=e3d96a76f4c8" target="_blank">' . esc_html__( 'hCaptcha', 'gf-hcaptcha' ) . '</a> ' . esc_html__( 'dashboard under the tab', 'gf-hcaptcha' ) . ' <strong>' . esc_html__( 'sites', 'gf-hcaptcha' ) . '</strong>.',
                        'label'                 => '<label for="hcaptcha-sitekey"><strong>' . esc_html__( 'Global site key', 'gf-hcaptcha' ) . '</strong></label>',
                        'type'                  => 'text',
                        'style'                 => 'width:350px;',
                        'required'              => true,
                        'autocomplete'          => 'off'
                    )
                )
            )
        );
    }

    public function pre_init() {
        parent::pre_init();
     
        if ( $this->is_gravityforms_supported() && class_exists( 'GF_Field' ) && $this->get_plugin_setting( 'hcaptcha-secretkey' ) && $this->get_plugin_setting( 'hcaptcha-sitekey' ) ) {
            require_once( 'includes/hcaptcha-field.php' );
        }
    }

    public function init_admin() {
        parent::init_admin();
     
        add_filter( 'gform_tooltips', array( $this, 'tooltips' ) );
        add_action( 'gform_field_appearance_settings', array( $this, 'field_appearance_settings' ), 10, 2 );
        add_action( 'gform_field_advanced_settings', array( $this, 'field_advanced_settings' ), 10, 2 );
    }

    // Add tooltips next to labels
    public function tooltips( $tooltips ) {
        $hcaptcha_tooltips = array(
            'hcaptcha_theme' => sprintf( '<h6>%s</h6>%s', esc_html__( 'Theme hCaptcha', 'gf-hcaptcha' ), esc_html__( 'Choose for the light or the dark theme.', 'gf-hcaptcha' ) ),
            'hcaptcha_size' => sprintf( '<h6>%s</h6>%s', esc_html__( 'Size hCaptcha', 'gf-hcaptcha' ), esc_html__( 'Choose for the standard or the compact size.', 'gf-hcaptcha' ) ),
            'hcaptcha_lang' => sprintf( '<h6>%s</h6>%s', esc_html__( 'Language hCaptcha', 'gf-hcaptcha' ), esc_html__( 'Choose the language displayed for the hCapctha widget.', 'gf-hcaptcha' ) ),
            'hcaptcha_sitekey' => sprintf( '<h6>%s</h6>%s', esc_html__( 'Overwriting site key hCaptcha', 'gf-hcaptcha' ), esc_html__( 'This will overwrite the global site key from the main hCaptcha settings page.', 'gf-hcaptcha' ) . '<br><br>' . esc_html__( 'Please insert your', 'gf-hcaptcha' ) . ' <a href="https://hCaptcha.com/?r=e3d96a76f4c8" target="_blank">' . esc_html__( 'hCaptcha', 'gf-hcaptcha' ) . '</a> ' . esc_html__( 'site key here. You can find this in your', 'gf-hcaptcha' ) . ' <a href="https://hCaptcha.com/?r=e3d96a76f4c8" target="_blank">' . esc_html__( 'hCaptcha', 'gf-hcaptcha' ) . '</a> ' . esc_html__( 'dashboard under the tab', 'gf-hcaptcha' ) . ' <strong>' . esc_html__( 'sites', 'gf-hcaptcha' ) . '</strong>.' ),
            'hcaptcha_modus' => sprintf( '<h6>%s</h6>%s', esc_html__( 'Mode hCaptcha', 'gf-hcaptcha' ), esc_html__( 'Choose for the visible or the invisible hCaptcha.', 'gf-hcaptcha' ) ),
            'hcaptcha_compat' => sprintf( '<h6>%s</h6>%s', esc_html__( 'reCAPTCHA compatibility', 'gf-hcaptcha' ), esc_html__( 'Check if including both hCaptcha and reCaptcha on the same page.', 'gf-hcaptcha' ) ),
        );
     
        return array_merge( $tooltips, $hcaptcha_tooltips );
    }

    // Add the hCaptcha appearance settings
    public function field_appearance_settings( $position, $form_id ) {
        if ( $position == 250 ) {
            require_once( 'includes/lang-choices.php' );
            ?>
            <li class="hcaptcha_theme field_setting">
                <label class="section_label">
                    <?php esc_html_e( 'Choose theme', 'gf-hcaptcha' ); ?>
                    <?php gform_tooltip( 'hcaptcha_theme' ) ?>
                </label>
                <div>
					<input type="radio" name="hcaptcha_theme" id="hcaptcha_theme_light" size="10" value="light" onclick="return SethCaptchaTheme( this.value );" onkeypress="return SethCaptchaTheme( this.value );">
					<label for="hcaptcha_theme_light" class="inline"><?php esc_html_e( 'Light', 'gf-hcaptcha' ); ?></label>

					<input type="radio" name="hcaptcha_theme" id="hcaptcha_theme_dark" size="10" value="dark" onclick="return SethCaptchaTheme( this.value );" onkeypress="return SethCaptchaTheme( this.value );">
					<label for="hcaptcha_theme_dark" class="inline"><?php esc_html_e( 'Dark', 'gf-hcaptcha' ); ?></label>
			    </div>
            </li>

            <li class="hcaptcha_size field_setting">
                <label class="section_label">
                    <?php esc_html_e( 'Choose size', 'gf-hcaptcha' ); ?>
                    <?php gform_tooltip( 'hcaptcha_size' ) ?>
                </label>
                <div>
					<input type="radio" name="hcaptcha_size" id="hcaptcha_size_standard" size="10" value="standard" onclick="return SethCaptchaSize( this.value );" onkeypress="return SethCaptchaSize( this.value );">
					<label for="hcaptcha_size_standard" class="inline"><?php esc_html_e( 'Standard', 'gf-hcaptcha' ); ?></label>

					<input type="radio" name="hcaptcha_size" id="hcaptcha_size_compact" size="10" value="compact" onclick="return SethCaptchaSize( this.value );" onkeypress="return SethCaptchaSize( this.value );">
					<label for="hcaptcha_size_compact" class="inline"><?php esc_html_e( 'Compact', 'gf-hcaptcha' ); ?></label>
			    </div>
            </li>

            <li class="hcaptcha_lang field_setting">
                <label class="section_label">
                    <?php esc_html_e( 'Choose language', 'gf-hcaptcha' ); ?>
                    <?php gform_tooltip( 'hcaptcha_lang' ) ?>
                </label>
                <div>
					<select name="hcaptcha_lang" id="hcaptcha_lang_selector" onchange="return SethCaptchaLang( this.value );">
                        <option value="default" selected><?php esc_html_e( 'Browser default', 'gf-hcaptcha' ); ?></option>
                        <?php foreach($languages as $code => $value) : ?>
                            <option value="<?php echo $code; ?>"><?php echo $value; ?></option>
                        <?php endforeach; ?>
                    </select>
			    </div>
            </li>
            <?php
        }
    }

    // Add the hCaptcha advanced settings
    public function field_advanced_settings( $position, $form_id ) {
        if ( $position == 250 ) {
            ?>
            <li class="hcaptcha_sitekey field_setting">
                <label for="hcaptcha_sitekey" class="section_label">
                    <?php esc_html_e( 'Overwriting site key', 'gf-hcaptcha' ); ?>
                    <?php gform_tooltip( 'hcaptcha_sitekey' ) ?>
                </label>
                <input type="text" id="hcaptcha_sitekey" size="35" onkeyup="return SethCaptchaSitekey( this.value );" onchange="return SethCaptchaSitekey( this.value );">
            </li>

            <li class="hcaptcha_modus field_setting">
                <label class="section_label">
                    <?php esc_html_e( 'Choose mode', 'gf-hcaptcha' ); ?>
                    <?php gform_tooltip( 'hcaptcha_modus' ) ?>
                </label>
                <div>
					<input type="radio" name="hcaptcha_modus" id="hcaptcha_modus_visible" size="10" value="visible" onclick="return SethCaptchaModus( this.value );" onkeypress="return SethCaptchaModus( this.value );">
					<label for="hcaptcha_modus_visible" class="inline"><?php esc_html_e( 'Visible', 'gf-hcaptcha' ); ?></label>

					<input type="radio" name="hcaptcha_modus" id="hcaptcha_modus_invisible" size="10" value="invisible" onclick="return SethCaptchaModus( this.value );" onkeypress="return SethCaptchaModus( this.value );">
					<label for="hcaptcha_modus_invisible" class="inline"><?php esc_html_e( 'Invisible', 'gf-hcaptcha' ); ?></label>
			    </div>
            </li>

            <li class="hcaptcha_compat field_setting">
                <label class="section_label">
                    <?php esc_html_e( 'reCAPTCHA compatibility', 'gf-hcaptcha' ); ?>
                    <?php gform_tooltip( 'hcaptcha_compat' ) ?>
                </label>
                <div>
					<input type="checkbox" name="hcaptcha_compat" id="hcaptcha_compat_disable" size="10" onclick="return SethCaptchaCompat( this.checked );" onkeypress="return SethCaptchaCompat( this.checked );">
					<label for="hcaptcha_compat_disable" class="inline"><?php esc_html_e( 'Disable', 'gf-hcaptcha' ); ?></label>
			    </div>
            </li>
            <?php
        }
    }
}