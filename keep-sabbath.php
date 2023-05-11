<?php declare(strict_types=1);
/*
 * Plugin Name:       Keep Sabbath
 * Plugin URI:        https://github.com/KeepSabbath/wp-keep-sabbath
 * Description:       Plugin to help you observe the Biblical Sabbath and Holy days by automatically redirecting specific pages on your site.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Noah Rahm
 * Author URI:        https://noahrahm.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/KeepSabbath/wp-keep-sabbath
 * Text Domain:       keep-sabbath
 * Domain Path:       /languages
 */

/*
Keep Sabbath is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Keep Sabbath is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Keep Sabbath. If not, see https://www.gnu.org/licenses/.
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

define( 'KEEPSABBATH_VERSION', '1.0.0' );
define( 'KEEPSABBATH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'KEEPSABBATH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'KEEPSABBATH_PLUGIN_FILE', __FILE__ );
define( 'KEEPSABBATH_PLUGIN_BASE', plugin_basename( __FILE__ ) );


/**
 * Main KeepSabbath Class.
 *
 * @since 1.0.0
 */
class KeepSabbath {
    /**
     * This plugin's instance.
     *
     * @var KeepSabbath
     * @since 1.0.0
     */
    private static $instance;

    /**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name = 'Keep Sabbath';

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version = KEEPSABBATH_VERSION;

    /**
     * Main KeepSabbath Instance.
     *
     * Insures that only one instance of KeepSabbath exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * @since 1.0.0
     * @static
     * @return object|KeepSabbath The one true KeepSabbath
     */
    public static function instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof KeepSabbath ) ) {
            self::$instance = new KeepSabbath();
            self::$instance->init();
            self::$instance->includes();
            self::$instance->define_admin_hooks();
        }
        return self::$instance;
    }

    public function init() {
        // Redirect
        add_action('template_redirect', array($this, 'redirect_if_sabbath'));

        // Add settings link to plugins page
        add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'add_plugin_page_settings_link'));
    }

    public function includes() {
        require_once KEEPSABBATH_PLUGIN_DIR . '/includes/sabbath.php';

        require_once KEEPSABBATH_PLUGIN_DIR . 'admin/class-keep-sabbath-admin.php';
    }

	/**
	 * Adds the settings link to the admin plugins page
	 *
	 * @since    1.0.0
	 * @access   public
	 */
    public function add_plugin_page_settings_link( $links ) {
        $links[] = '<a href="' .
            admin_url( 'options-general.php?page=keepsabbath' ) .
            '">' . __('Settings') . '</a>';
        return $links;
    }

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Keep_Sabbath_Admin($this->plugin_name, $this->version);

        add_action( 'admin_init', array($plugin_admin, 'register_settings'));
        add_action( 'admin_menu', array($plugin_admin, 'setup_menu')); 

	}
    
    /**
     * Get an array from a line separated list of items in an admin textarea option.
     * 
     * @since 1.0.0
     *
     * @param string $option_id The id of the option
     * @return array<string> 
     */
    private function get_array_from_textarea_option(string $option_id) {
		$options_str = preg_replace( '/\v(?:[\v\h]+)/', "\n", trim( strval( get_option( $option_id, '' ) ) ) );
		$options_str = ( ! empty( $options_str ) ) ? $options_str : '';
		return explode( "\n", $options_str );
    }

    /**
     * Get the array of holy day DateTime objects from the admin option
     * 
     * @since 1.0.0
     *
     * @return array<DateTime> 
     */
    function get_holy_days_option() {
        $holy_days = array();
        $holy_day_dates = $this->get_array_from_textarea_option('keepsabbath_setting_holy_day_dates');

        foreach ($holy_day_dates as $key => $val) {
            $day = DateTime::createFromFormat('m/d/Y', $val);
            array_push($holy_days, $day);
        }
        return $holy_days;
    }

    /**
     * Get the latitude value from the admin option
     * 
     * @since 1.0.0
     *
     * @return float
     */
    function get_latitude_option() {
        return trim( get_option('keepsabbath_setting_latitude') ) + 0.0;
    }

    /**
     * Get the longitude value from the admin option
     * 
     * @since 1.0.0
     *
     * @return float
     */
    function get_longitude_option() {
        return trim( get_option('keepsabbath_setting_longitude') ) + 0.0;
    }

    /**
     * Get the timezone value from the admin option
     * 
     * @since 1.0.0
     *
     * @return string
     */
    function get_timezone_option() {
        $timezone = get_option('keepsabbath_setting_date_timezone');
        return $timezone != '' ? $timezone : 'America/Chicago';
    }

    /**
     * Get the array of pages to redirect from the admin option
     * 
     * @since 1.0.0
     *
     * @return array<string> 
     */
    function get_page_urls_to_redirect_option() {
        $pages_to_redirect = $this->get_array_from_textarea_option('keepsabbath_setting_pages_to_redirect');
        return $pages_to_redirect;
    }

    /**
     * Get the redirect to page URL value from the admin option
     * 
     * @since 1.0.0
     *
     * @return string
     */
    function get_redirect_to_page_url_option() {
        return trim( get_option('keepsabbath_setting_redirect_to_page') );
    }
    

    /**
     * Action callback to redirect pages if it is the Sabbath or a Holy day.
     * 
     * @since 1.0.0
     *
     * @return string
     */
    function redirect_if_sabbath() {
        global $wp;

        // Get options from the admin
        $timezone = $this->get_timezone_option();
        $holy_days = $this->get_holy_days_option();
        $lat = $this->get_latitude_option();
        $lng = $this->get_longitude_option();
        $urls_to_redirect = $this->get_page_urls_to_redirect_option();
        $redirect_to_url = $this->get_redirect_to_page_url_option();

        // Set the time zone
        date_default_timezone_set($timezone);

        // Get whether it is the Sabbath or a Holy day
        $datetime = new Keep_Sabbath_DateTime();
        $is_holy_day = $datetime->is_sabbath_or_holy_day($holy_days, $lat, $lng);
        //echo $is_holy_day ? "YES" : "NO";
        if ($is_holy_day && $urls_to_redirect != array() && $redirect_to_url != '') {

            // Check the given redirect page URLs and redirect if 
            // the website visitor is requesting that URL.
            foreach ($urls_to_redirect as $key => $val) {
                if ( $wp->request == $val ) {
                    wp_redirect( site_url( $redirect_to_url ) );
                    exit;
                }
            }
        }
    }
}

/**
 * The main function for that returns KeepSabbath
 *
 * The main function responsible for returning the one true KeepSabbath
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $keepsabbath = KeepSabbath(); ?>
 *
 * @since 1.0.0
 * @return object|KeepSabbath The one true KeepSabbath Instance.
 */
function keep_sabbath_init() {
    KeepSabbath::instance();
}

add_action('plugins_loaded', 'keep_sabbath_init');

