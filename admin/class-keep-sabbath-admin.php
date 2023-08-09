<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Keep_Sabbath
 * @subpackage Keep_Sabbath/admin
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


class Keep_Sabbath_Admin {

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
     * The prefix to be used in this plugin
     *
     * @since  	1.0.0
     * @access 	private
     * @var  	string 		$prefix 	Prefix for names in this plugin
    */
    private $prefix = 'keepsabbath_setting'; 

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}


    /**
     * Include the setting page
     *
     * @since  1.0.0
     * @access public
    */
    function init(){
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        include KEEPSABBATH_PLUGIN_DIR . 'admin/partials/keep-sabbath-admin-display.php' ;
        
    } 

    /**
     * Setup the admin menu
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function setup_menu(){
        // Add link to Settings menu
        add_options_page(
            'Keep Sabbath', 
            'Keep Sabbath', 
            'manage_options', 
            'keepsabbath', 
            array( $this, 'init' )
        );
	} 


    /**
     * Register the setting parameters
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function register_settings() {
        // Add Your Location section
        add_settings_section(
            $this->prefix . '_your_location_section',
            __( 'Your Location', 'keepsabbath' ),
            array( $this, 'your_location_section' ),
            $this->plugin_name
        );

        add_settings_field(
            $this->prefix . '_latitude',
            __( 'Latitude', 'keepsabbath' ),
            array( $this, 'latitude_input' ),
            $this->plugin_name,
            $this->prefix . '_your_location_section',
            array( 'label_for' => $this->prefix . '_latitude' )
        );

        add_settings_field(
            $this->prefix . '_longitude',
            __( 'Longitude', 'keepsabbath' ),
            array( $this, 'longitude_input' ),
            $this->plugin_name,
            $this->prefix . '_your_location_section',
            array( 'label_for' => $this->prefix . '_longitude' )
        );

        // Add Holy Day Dates section
        add_settings_section(
            $this->prefix . '_holy_day_dates_section',
            __( 'Holy Day Dates', 'keepsabbath' ),
            array( $this, 'holy_day_dates_section' ),
            $this->plugin_name
        );

        add_settings_field(
            $this->prefix . '_holy_day_dates',
            __( 'Holy Days (One date per line in the format MM/DD/YYYY)', 'keepsabbath' ),
            array( $this, 'holy_day_dates_textarea' ),
            $this->plugin_name,
            $this->prefix . '_holy_day_dates_section',
            array( 'label_for' => $this->prefix . '_holy_day_dates' )
        );

        // Add Page Redirect section
        add_settings_section(
            $this->prefix . '_page_redirect_section',
            __( 'Page Redirect Settings', 'keepsabbath' ),
            array( $this, 'page_redirect_settings_section' ),
            $this->plugin_name
        );

        add_settings_field(
            $this->prefix . '_pages_to_redirect',
            __( 'Page URLs (One per line)', 'keepsabbath' ),
            array( $this, 'pages_to_redirect_textarea' ),
            $this->plugin_name,
            $this->prefix . '_page_redirect_section',
            array( 'label_for' => $this->prefix . '_pages_to_redirect' )
        );

        add_settings_field(
            $this->prefix . '_redirect_to_page',
            __( 'Redirect to page URL', 'keepsabbath' ),
            array( $this, 'redirect_to_page_input' ),
            $this->plugin_name,
            $this->prefix . '_page_redirect_section',
            array( 'label_for' => $this->prefix . '_redirect_to_page' )
        );


        // Register settings
        register_setting( 
            $this->plugin_name, 
            $this->prefix . '_latitude', 
            array(
                'type'              => 'float',
                'show_in_rest'      => true,
                'sanitize_callback' => array( $this, 'sanitize_latitude_input'),
            )
        );
        register_setting( 
            $this->plugin_name, 
            $this->prefix .'_longitude', 
            array(
                'type'              => 'float',
                'show_in_rest'      => true,
                'sanitize_callback' => array( $this, 'sanitize_longitude_input'),
            )
        );

        register_setting( 
            $this->plugin_name, 
            $this->prefix . '_holy_day_dates', 
            array(
                'type'              => 'string',
                'show_in_rest'      => true,
                'sanitize_callback' => array( $this, 'sanitize_holy_day_dates_textarea'),
            )
        );
        register_setting( 
            $this->plugin_name, 
            $this->prefix . '_pages_to_redirect', 
            array(
                'type'              => 'string',
                'show_in_rest'      => true,
                'sanitize_callback' => array( $this, 'sanitize_pages_to_redirect_textarea'),
            )
        );
        register_setting( 
            $this->plugin_name, 
            $this->prefix . '_redirect_to_page', 
            array(
                'type'              => 'url',
                'show_in_rest'      => true,
                'sanitize_callback' => array( $this, 'sanitize_redirect_to_page_input'),
            )
        );
    } 

    /**
     * Render the text for the Your Location section
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function your_location_section() {
        echo '<p>' . __( 'Used to accurately calculate the sunset times for your area. You can use any latitude/longitude finder online to find the right coordinates from your address. (e.g: Latitude 38.895438 Longitude -77.031281)', 'keepsabbath' ) . '</p>';
    } 

    /**
     * Render the Latitude input
     *
     * @since  1.0.0
     * @access public
     */
    public function latitude_input() {
        $val = get_option( 'keepsabbath_setting_latitude' );
        echo '<input type="text" name="keepsabbath_setting_latitude" id="keepsabbath_setting_latitude" value="' . esc_attr($val) . '"> ';
    } 

    /**
     * Sanitize the Latitude input
     *
     * @since  1.0.0
     * @access public
     */
    public function sanitize_latitude_input($input) {
        $old_option = get_option( $this->prefix . '_latitude' );

        if ( filter_var($input, FILTER_VALIDATE_FLOAT)!== false ) {
            return $input;
        } else {
            add_settings_error( $this->prefix . '_latitude', esc_attr('settings_updated'), __('Incorrect latitude.'), 'error' );
            return $old_option;
        }
    } 

    /**
     * Render the Longitude input
     *
     * @since  1.0.0
     * @access public
     */
    public function longitude_input() {
        $val = get_option( 'keepsabbath_setting_longitude' );
        echo '<input type="text" name="keepsabbath_setting_longitude" id="keepsabbath_setting_longitude" value="' . esc_attr($val) . '"> ';
    } 

    /**
     * Sanitize the Longitude input
     *
     * @since  1.0.0
     * @access public
     */
    public function sanitize_longitude_input($input) {
        $old_option = get_option( $this->prefix . '_longitude' );

        if ( filter_var($input, FILTER_VALIDATE_FLOAT)!== false ) {
            return $input;
        } else {
            add_settings_error( $this->prefix . '_longitude', esc_attr('settings_updated'), __('Incorrect longitude.'), 'error' );
            return $old_option;
        }
    } 

    /**
     * Render the text for the Holy Day Dates section
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function holy_day_dates_section() {
        echo '<p>' . __( 'A list of the Holy day dates in the MM/DD/YYYY format (e.g: 05/10/2023).', 'keepsabbath' ) . '</p>';
    } 

    /**
     * Render the textfield for the Holy Days
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function holy_day_dates_textarea() {
        echo '<textarea name="keepsabbath_setting_holy_day_dates" type="textarea" cols="" rows="7">';
        $val = get_option( 'keepsabbath_setting_holy_day_dates' );
		if ( $val != "" ) {
			echo esc_textarea($val);
		}
		echo '</textarea>';
    }

    /**
     * Sanitize the textfield for the Holy Days
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function sanitize_holy_day_dates_textarea($input) {
        return sanitize_textarea_field($input);
    }

    /**
     * Render the text for the Page Redirect Settings section
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function page_redirect_settings_section() {
        echo '<p>' . __( 'Redirect pages to a certain URL when it is the Sabbath or a Holy day.', 'keepsabbath' ) . '</p>';
    } 

    /**
     * Render the textfield for the Page Redirect Settings
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function pages_to_redirect_textarea() {
        echo '<textarea name="keepsabbath_setting_pages_to_redirect" type="textarea" cols="" rows="7">';
        $val = get_option( 'keepsabbath_setting_pages_to_redirect' );
		if ( $val != "" ) {
			echo esc_textarea($val);
		}
		echo '</textarea>';
    }

    /**
     * Sanitize the textfield for the Page Redirect Settings
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function sanitize_pages_to_redirect_textarea($input) {
        return sanitize_textarea_field($input);
    }

    /**
     * Render the Redirect to Page URL input
     *
     * @since  1.0.0
     * @access public
     */
    public function redirect_to_page_input() {
        $val = get_option( 'keepsabbath_setting_redirect_to_page' );
        echo '<input type="text" name="keepsabbath_setting_redirect_to_page" id="keepsabbath_setting_redirect_to_page" value="' . esc_url($val) . '"> ';
    } 

    /**
     * Sanitize the Redirect to Page URL input
     *
     * @since  1.0.0
     * @access public
     */
    public function sanitize_redirect_to_page_input($input) {
        $old_option = get_option( $this->prefix . '_redirect_to_page' );

        if ( wp_validate_redirect($input)!== '' ) {
            return sanitize_url($input);
        } else {
            add_settings_error( $this->prefix . '_redirect_to_page', esc_attr('settings_updated'), __('Incorrect page redirect URL.'), 'error' );
            return $old_option;
        }
    } 
}