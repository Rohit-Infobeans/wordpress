<?php
/*
  Plugin Name: Custom Registration
  Plugin URI: http://code.tutsplus.com
  Description: Updates user rating based on number of posts.
  Version: 1.0
  Author: Agbonghama Collins
  Author URI: http://tech4sky.com
 */
 //Used for including js
 
$dir = plugin_dir_path( __FILE__ );
require_once($dir.'eve-reg-display.php');
 
 function event_manager()
{
        // do NOT forget this global
	global $wpdb;
      $appointment = $wpdb->prefix.  'event_reg';
	// this if statement makes sure that the table doe not exist already
	if($wpdb->get_var("show tables like my_table_name") != $appointment) 
	{
		$sql = "CREATE TABLE $appointment (
		`Eve_id` mediumint(9) NOT NULL AUTO_INCREMENT,
            `Eve_Title` varchar(50), 
            `Eve_Desc` varchar(200),
            `Eve_Venue` varchar(100),
            `Eve_Sdate` varchar(10),
            `Eve_Stime` varchar(10),
            `Eve_Tdate` varchar(10),
            `Eve_Ttime` varchar(10),
            `Eve_Users` varchar(200),
            `Eve_Author` varchar(20),
            `Eve_datecreated` varchar(10),
            `Eve_Status` int(1),
		UNIQUE KEY id (Eve_id)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}
// this hook will cause our creation function to run when the plugin is activated
register_activation_hook( __FILE__, 'event_manager' );

function complete_registration() {
    global $reg_errors, $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        $userdata = array(
        'user_login'    =>   $username,
        'user_email'    =>   $email,
        'user_pass'     =>   $password,
        'user_url'      =>   $website,
        'first_name'    =>   $first_name,
        'last_name'     =>   $last_name,
        'nickname'      =>   $nickname,
        'description'   =>   $bio,
        );
        $user = wp_insert_user( $userdata );
        echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';   
    }
}
function custom_registration_function() {
    if ( isset($_POST['submit'] ) ) {
        registration_validation(
        $_POST['username'],
        $_POST['password'],
        $_POST['email'],
        $_POST['website'],
        $_POST['fname'],
        $_POST['lname'],
        $_POST['nickname'],
        $_POST['bio']
        );
         
        // sanitize user form input
        global $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
        $username   =   sanitize_user( $_POST['username'] );
        $password   =   esc_attr( $_POST['password'] );
        $email      =   sanitize_email( $_POST['email'] );
        $website    =   esc_url( $_POST['website'] );
        $first_name =   sanitize_text_field( $_POST['fname'] );
        $last_name  =   sanitize_text_field( $_POST['lname'] );
        $nickname   =   sanitize_text_field( $_POST['nickname'] );
        $bio        =   esc_textarea( $_POST['bio'] );
 
        // call @function complete_registration to create the user
        // only when no WP_error is found
        complete_registration(
        $username,
        $password,
        $email,
        $website,
        $first_name,
        $last_name,
        $nickname,
        $bio
        );
    }
 
    registration_form(
        $username,
        $password,
        $email,
        $website,
        $first_name,
        $last_name,
        $nickname,
        $bio
        );
}
// Register a new shortcode: [cr_custom_registration]
add_shortcode( 'cr_custom_registration', 'custom_registration_shortcode' );
 
// The callback function that will replace [book]
function custom_registration_shortcode() {
    ob_start();
    custom_registration_function();
    return ob_get_clean();
}