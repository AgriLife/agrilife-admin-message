<?php
/**
 * Plugin Name: AgriLife Admin Message
 * Plugin URI: https://github.com/AgriLife/agrilife-admin-message
 * Description: Dashboard message plugin
 * Version: 1.0.0
 * Author: Zachary K. Watkins
 * Author URI: https://github.com/ZachWatkins
 * Author Email: zachary.watkins@ag.tamu.edu
 * License: GPL2+
 */

define( 'AAM_DIR_PATH', plugin_dir_path( __FILE__ ) );

// Add the settings page
include( AAM_DIR_PATH . 'src/Settings.php' );
$AAMAdmin = new \AgriLife\Admin\Message;
$AAMAdmin->init();

add_action( 'admin_notices', 'aam_show_message' );

function aam_show_message(){
	$options = get_site_option('agrilife_message_settings');
	$blog_details = get_blog_details();
	$show_message = false;

	if( $options['show_message_everywhere'] === 'on' ){
		$show_message = true;
	} else if( $options['show_message_nonpublic'] === 'on' && !$blog_details->public ){
		$show_message = true;
	}

	if( $show_message ){

		$message = stripslashes( $options['message'] );
    $message = html_entity_decode( $message );

    // Handle merge tags in message
    if( strpos( $message, '{site_url}' ) ){
    	$message = str_replace('{site_url}', get_site_url(), $message);
    }

    if( strpos( $message, '{user_email}' ) ){
	    $current_user = wp_get_current_user();
	    $message = str_replace('{user_email}', $current_user->user_email, $message);
    }

    if( !empty( $message ) ){

		  ?>
		  <div class="notice notice-warning">
		      <p><?php echo $message; ?></p>
		  </div>
		  <?php

    }

	}
}
