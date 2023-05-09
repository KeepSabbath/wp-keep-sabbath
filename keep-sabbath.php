<?php declare(strict_types=1);
/*
 * Plugin Name:       Keep Sabbath
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Plugin to help you observe the Biblical Sabbath and Holy days by automatically closing your Woocommerce shop or specific pages on your site.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Noah Rahm
 * Author URI:        https://noahrahm.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       keep-sabbath-plugin
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


// Set timezone with values from https://www.php.net/manual/en/timezones.php
date_default_timezone_set('America/Chicago');


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

        // Add Javascript and CSS for admin screens
        //add_action('admin_enqueue_scripts', array($this,'enqueueAdmin'));

        // Add Javascript and CSS for front-end display
        //add_action('wp_enqueue_scripts', array($this,'enqueue'));
        add_action('template_redirect', array($this, 'redirect_if_sabbath'));
    }


    public function includes() {
        require_once KEEPSABBATH_PLUGIN_DIR . '/includes/sabbath.php';

        require_once KEEPSABBATH_PLUGIN_DIR . 'admin/class-keep-sabbath-admin.php';
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

		add_action( 'admin_enqueue_scripts', array($plugin_admin, 'enqueue_styles'));
		add_action( 'admin_enqueue_scripts', array($plugin_admin, 'enqueue_scripts'));

        add_action( 'admin_init', array($plugin_admin, 'register_settings'));
        add_action( 'admin_menu', array($plugin_admin, 'setup_menu')); 

	}

    
    function redirect_if_sabbath() {
        global $wp;

        $days = array(
            DateTime::createFromFormat('d/m/Y', '09/05/2023'),
            DateTime::createFromFormat('d/m/Y', '12/05/2023'),
        );

        // WIP
        // Checkout will be closed while we observe the Sabbath from 7:45pm ET Fri until 9:15pm ET Sat.
        // Checkout is closed 

        $datetime = new Keep_Sabbath_DateTime();
        $is_holy_day = $datetime->is_sabbath_or_holy_day($days, 36.952141, -92.660400);
        //echo $is_holy_day ? "YES" : "NO";
        if ($is_holy_day) {
            if( $wp->request == 'checkout' ) {
                wp_redirect( site_url( '/' ) );
                exit;
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

