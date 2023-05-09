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
        // Add a General section
        add_settings_section(
            $this->option_name. '_general',
            __( 'General', 'keepsabbath' ),
            array( $this, $this->option_name . '_general_cb' ),
            $this->plugin_name
        );
        // Add a boolean field
        add_settings_field(
            $this->option_name . '_bool',
            __( 'Boolean setting', 'keepsabbath' ),
            array( $this, $this->option_name . '_bool_cb' ),
            $this->plugin_name,
            $this->option_name . '_general',
            array( 'label_for' => $this->option_name . '_bool' )
        );
        // Add a numeric field
        add_settings_field(
            $this->option_name . '_number',
            __( 'Number setting', 'keepsabbath' ),
            array( $this, $this->option_name . '_number_cb' ),
            $this->plugin_name,
            $this->option_name . '_general',
            array( 'label_for' => $this->option_name . '_number' )
        );
        // Register the boolean field
        register_setting( $this->plugin_name, $this->option_name . '_bool', array( $this, $this->option_name . '_sanitize_bool' ) );
        // Register the numeric field
        register_setting( $this->plugin_name, $this->option_name . '_number', 'integer' );
    } 

    /**
     * Render the text for the general section
     *
     * @since  	1.0.0
     * @access 	public
    */
    public function keepsabbath_setting_general_cb() {
        echo '<p>' . __( 'Settings.', 'keepsabbath' ) . '</p>';
    } 

    /**
     * Render the number input for this plugin
     *
     * @since  1.0.0
     * @access public
     */
    public function keepsabbath_setting_number_cb() {
        $val = get_option( $this->option_name . '_number' );
        echo '<input type="text" name="' . $this->option_name . '_number' . '" id="' . $this->option_name . '_number' . '" value="' . $val . '"> ' . __( '(?)', 'keepsabbath' );
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