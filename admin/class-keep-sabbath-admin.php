<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Keep_Sabbath
 * @subpackage Keep_Sabbath/admin
 */


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
		add_menu_page( 'Keep Sabbath Settings', 'Keep Sabbath', 'manage_options', 'keepsabbath', array($this, 'init'), 'dashicons-welcome-learn-more' );
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

        // Add Holy Day Dates section
        add_settings_section(
            'keepsabbath_setting_holy_day_dates_section',
            __( 'Holy Day Dates', 'keepsabbath' ),
            array( $this, 'holy_day_dates_section_cb' ),
            $this->plugin_name
        );
        // Add a boolean field
        // add_settings_field(
        //     $this->option_name . '_bool',
        //     __( 'Boolean setting', 'keepsabbath' ),
        //     array( $this, $this->option_name . '_bool_cb' ),
        //     $this->plugin_name,
        //     $this->option_name . '_your_location_section',
        //     array( 'label_for' => $this->option_name . '_bool' )
        // );

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


        // Register the boolean field
        //register_setting( $this->plugin_name, $this->option_name . '_bool', array( $this, $this->option_name . '_sanitize_bool' ) );
        // Register the numeric field
        register_setting( $this->plugin_name, 'keepsabbath_setting_latitude', 'integer' );
        register_setting( $this->plugin_name, 'keepsabbath_setting_longitude', 'integer' );

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


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );

	}

}