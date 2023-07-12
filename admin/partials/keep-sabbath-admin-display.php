<?php
/**
 * Provide an admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Keep_Sabbath
 * @subpackage Keep_Sabbath/admin/partials
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}
?>
<div class="wrap">
 <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
 <form action="options.php" method="post">
     <?php
         settings_errors();
         settings_fields( $this->plugin_name );
         do_settings_sections( $this->plugin_name );
         submit_button(); ?>
 </form>
</div> 