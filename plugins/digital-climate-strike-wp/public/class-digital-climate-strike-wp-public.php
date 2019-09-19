<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/fightforthefuture
 * @since      1.0.0
 *
 * @package    Digital_Climate_Strike_WP
 * @subpackage Digital_Climate_Strike_WP/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Digital_Climate_Strike_WP
 * @subpackage Digital_Climate_Strike_WP/public
 * @author     Fight For the Future <team@fightforthefuture.org>
 */
class Digital_Climate_Strike_WP_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
     * This method simply adds the Digital Climate strike widget to the wordpress public site.
     */
    public function add_partial() {
        include 'partials/digital-climate-strike-wp-public-display.php';
    }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Digital_Climate_Strike_WP_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Digital_Climate_Strike_WP_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/digital-climate-strike-wp-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Digital_Climate_Strike_WP_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Digital_Climate_Strike_WP_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/digital-climate-strike-wp-public.js', array( 'jquery' ), $this->version, false );
	}


    /**
     * This method is used by the public-display partial to instantiate the Javascript Date objects with the
     * correct integers.
     *
     * @param $format
     * @param $input
     * @return int
     */
    public function get_date_field($format, $input) {
        $value = date($input);
        return (int)date($format, strtotime('-1 months', strtotime($value)));
    }
}
