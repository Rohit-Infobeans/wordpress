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
 
<<<<<<< HEAD
$dir = plugin_dir_path( __FILE__ );
require_once($dir.'eve-reg-display.php');
require_once($dir.'event_list.php');
 
 //Used to create table for plugin when activated
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
//--------------------------------------------------------------------------------------------------



//Function to insert data in table
function complete_registration() {
      $current_user = wp_get_current_user();
      $dc = date("Y-m-d");
      $status = 0;
      $cuser = $current_user->user_login;
      $user = $_POST['traditional'];
      $u = implode(",", $user);
     global $wpdb, $table_prefix;
    $tablename =  $table_prefix . 'event_reg';

    $data = array( 
     'Eve_Title' => $_POST['event_title'], 
        'Eve_Desc' => $_POST['desc'],
        'Eve_Venue' => $_POST['venue'], 
        'Eve_Sdate' => $_POST['start_date'],
        'Eve_Stime' => $_POST['start_time'], 
        'Eve_Tdate' => $_POST['to_date'], 
        'Eve_Ttime' => $_POST['to_time'], 
        'Eve_Users' => $u,
        'Eve_Author' => $cuser,
        'Eve_datecreated' => $dc,
        'Eve_Status' => $status);

    // Debugging: Lets see what we're trying to save
    

    // FOR database SQL injection security, set up the formats
    $formats = array( 
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%d'
    ); 


    // Actually attempt to insert the data
    $wpdb->insert($tablename, $data, $formats);
}


function custom_registration_function() {
    if ( isset($_POST['submit'] ) ) {
        registration_validation(
        $_POST['event_title'],
        $_POST['start_date'],
        $_POST['start_time'],
        $_POST['to_date'],
        $_POST['to_time'],
        $_POST['venue'],
        $_POST['traditional'],
        $_POST['desc']
        );
         
        // sanitize user form input
        global $title, $sdate, $stime, $tdate, $ttime, $venue, $users, $desc, $status;
        $title   =   $_POST['event_title'] ;
        $sdate   =   $_POST['start_date'] ;
        $stime      = $_POST['start_time'];
        $tdate    = $_POST['to_date'] ;
        $ttime =   $_POST['to_time'];
        $venue  =   $_POST['venue'] ;
        $users   =   $_POST['traditional'] ;
        $desc        =   $_POST['desc'] ;
        $status = 0;
        
        mail_event(
        
        );
 
        // call @function complete_registration to create the user
        // only when no WP_error is found
        complete_registration(
        $title,
        $sdate,
        $stime,
        $tdate,
        $ttime,
        $venue,
        $users,
        $desc,
        $status
        );
    }
 
    registration_form(
        $eve_title, 
        $eve_sdate, 
        $eve_stime, 
        $eve_tdate, 
        $eve_ttime, 
        $eve_venue, 
        $eve_users,
        $desc
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

add_shortcode( 'all_eve_list', 'all_eve_list_shortcode' );
 
// The callback function that will replace [book]
function all_eve_list_shortcode() {
    ob_start();
    all_event_list();
    return ob_get_clean();
}