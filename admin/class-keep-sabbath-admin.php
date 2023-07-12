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
     * The options name to be used in this plugin
     *
     * @since  	1.0.0
     * @access 	private
     * @var  	string 		$option_name 	Option name of this plugin
    */
    private $option_name = 'keepsabbath_setting'; 

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
            'keepsabbath_setting_your_location_section',
            __( 'Your Location', 'keepsabbath' ),
            array( $this, 'your_location_section_cb' ),
            $this->plugin_name
        );

        add_settings_field(
            'keepsabbath_setting_latitude',
            __( 'Latitude', 'keepsabbath' ),
            array( $this, 'setting_setting_latitude_cb' ),
            $this->plugin_name,
            'keepsabbath_setting_your_location_section',
            array( 'label_for' => 'keepsabbath_setting_latitude' )
        );

        add_settings_field(
            'keepsabbath_setting_longitude',
            __( 'Longitude', 'keepsabbath' ),
            array( $this, 'setting_setting_longitude_cb' ),
            $this->plugin_name,
            'keepsabbath_setting_your_location_section',
            array( 'label_for' => 'keepsabbath_setting_longitude' )
        );

        add_settings_field(
            'keepsabbath_setting_date_timezone',
            __( 'Time Zone', 'keepsabbath' ),
            array( $this, 'date_timezone_cb' ),
            $this->plugin_name,
            'keepsabbath_setting_your_location_section',
            array( 'label_for' => 'keepsabbath_setting_date_timezone' )
        );

        // Add Holy Day Dates section
        add_settings_section(
            'keepsabbath_setting_holy_day_dates_section',
            __( 'Holy Day Dates', 'keepsabbath' ),
            array( $this, 'holy_day_dates_section_cb' ),
            $this->plugin_name
        );

        add_settings_field(
            'keepsabbath_setting_holy_day_dates',
            __( 'Holy Days (One date per line in the format MM/DD/YYYY)', 'keepsabbath' ),
            array( $this, 'holy_day_dates_textarea_cb' ),
            $this->plugin_name,
            'keepsabbath_setting_holy_day_dates_section',
            array( 'label_for' => 'keepsabbath_setting_holy_day_dates' )
        );

        // Add Page Redirect section
        add_settings_section(
            'keepsabbath_setting_page_redirect_section',
            __( 'Page Redirect Settings', 'keepsabbath' ),
            array( $this, 'page_redirect_settings_section_cb' ),
            $this->plugin_name
        );

        add_settings_field(
            'keepsabbath_setting_pages_to_redirect',
            __( 'Page URLs (One per line)', 'keepsabbath' ),
            array( $this, 'pages_to_redirect_textarea_cb' ),
            $this->plugin_name,
            'keepsabbath_setting_page_redirect_section',
            array( 'label_for' => 'keepsabbath_setting_pages_to_redirect' )
        );

        add_settings_field(
            'keepsabbath_setting_redirect_to_page',
            __( 'Redirect to page URL', 'keepsabbath' ),
            array( $this, 'redirect_to_page_cb' ),
            $this->plugin_name,
            'keepsabbath_setting_page_redirect_section',
            array( 'label_for' => 'keepsabbath_setting_redirect_to_page' )
        );

        register_setting( $this->plugin_name, 'keepsabbath_setting_latitude', 'integer' );
        register_setting( $this->plugin_name, 'keepsabbath_setting_longitude', 'integer' );
        register_setting( $this->plugin_name, 'keepsabbath_setting_date_timezone', 'array' );

        register_setting( $this->plugin_name, 'keepsabbath_setting_holy_day_dates', 'string' );
        register_setting( $this->plugin_name, 'keepsabbath_setting_pages_to_redirect', 'string' );
        register_setting( $this->plugin_name, 'keepsabbath_setting_redirect_to_page', 'string' );
    } 

    /**
     * Render the text for the Your Location section
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function your_location_section_cb() {
        echo '<p>' . __( 'Used to accurately calculate the sunset times for your area. You can use any latitude/longitude finder online to find the right coordinates from your address. (e.g: Latitude 38.895438 Longitude -77.031281)', 'keepsabbath' ) . '</p>';
    } 

    /**
     * Render the Latitude input
     *
     * @since  1.0.0
     * @access public
     */
    public function setting_setting_latitude_cb() {
        $val = get_option( 'keepsabbath_setting_latitude' );
        echo '<input type="text" name="keepsabbath_setting_latitude" id="keepsabbath_setting_latitude" value="' . $val . '"> ';
    } 

    /**
     * Render the Longitude input
     *
     * @since  1.0.0
     * @access public
     */
    public function setting_setting_longitude_cb() {
        $val = get_option( 'keepsabbath_setting_longitude' );
        echo '<input type="text" name="keepsabbath_setting_longitude" id="keepsabbath_setting_longitude" value="' . $val . '"> ';
    } 

    /**
     * Render the date timezone dropdown
     *
     * @since  1.0.0
     * @access public
     */
    public function date_timezone_cb() {
        // Values from https://www.php.net/manual/en/timezones.php
        $timezone_identifiers = DateTimeZone::listIdentifiers();

        $default = get_option( 'keepsabbath_setting_date_timezone' );
        echo '<select id="keepsabbath_setting_date_timezone" name="keepsabbath_setting_date_timezone"';
        foreach ($timezone_identifiers as $key => $val) {
            echo '<option value="'. $val .'"' . ($default == $val ? 'selected' : '') .'>';
            echo $val;
            echo '</option>';
        }
        echo '</select>';
    }

    /**
     * Render the text for the Holy Day Dates section
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function holy_day_dates_section_cb() {
        echo '<p>' . __( 'A list of the Holy day dates in the MM/DD/YYYY format (e.g: 05/10/2023).', 'keepsabbath' ) . '</p>';
    } 

    /**
     * Render the textfield for the Holy Days
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function holy_day_dates_textarea_cb() {
        echo '<textarea name="keepsabbath_setting_holy_day_dates" type="textarea" cols="" rows="7">';
        $val = get_option( 'keepsabbath_setting_holy_day_dates' );
		if ( $val != "") {
			echo $val;
		}
		echo '</textarea>';
    }

    /**
     * Render the text for the Page Redirect Settings section
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function page_redirect_settings_section_cb() {
        echo '<p>' . __( 'Redirect pages to a certain URL when it is the Sabbath or a Holy day.', 'keepsabbath' ) . '</p>';
    } 

    /**
     * Render the textfield for the Page Redirect Settings
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function pages_to_redirect_textarea_cb() {
        echo '<textarea name="keepsabbath_setting_pages_to_redirect" type="textarea" cols="" rows="7">';
        $val = get_option( 'keepsabbath_setting_pages_to_redirect' );
		if ( $val != "") {
			echo $val;
		}
		echo '</textarea>';
    }

    /**
     * Render the Redirect to Page URL input
     *
     * @since  1.0.0
     * @access public
     */
    public function redirect_to_page_cb() {
        $val = get_option( 'keepsabbath_setting_redirect_to_page' );
        echo '<input type="text" name="keepsabbath_setting_redirect_to_page" id="keepsabbath_setting_redirect_to_page" value="' . $val . '"> ';
    } 

    /**
     * Render the radio input field for boolean option
     *
     * @since  1.0.0
     * @access public
    */
    public function keepsabbath_setting_bool_cb() {
        $val = get_option( $this->option_name . '_bool' );
        ?>
            <fieldset>
                <label>
                    <input type="radio" name="<?php echo $this->option_name . '_bool' ?>" id="<?php echo $this->option_name . '_bool' ?>" value="true" <?php checked( $val, 'true' ); ?>>
                    <?php _e( 'True', 'keepsabbath' ); ?>
                </label>
                <br>
                <label>
                    <input type="radio" name="<?php echo $this->option_name . '_bool' ?>" value="false" <?php checked( $val, 'false' ); ?>>
                    <?php _e( 'False', 'keepsabbath' ); ?>
                </label>
            </fieldset>
        <?php
    } 
}