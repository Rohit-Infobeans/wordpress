<?php
/*
      Plugin Name: Custom Registration
      Plugin URI:
      Description: 
      Version:
      Author: Infobeans
      Author URI: 
*/
//Used for including other php files

$dir = plugin_dir_path( __FILE__ );
require_once($dir.'eve-reg-display.php');
require_once($dir.'event_list.php');
require_once($dir.'invitation.php');
require_once($dir.'dash_cal.php');

global $eventtable, $usertable;
//Table Names
$eventtable =  $table_prefix . 'calendar';
$usertable =  $table_prefix . 'event_users';

//Used to create table for plugin when activated
function event_manager()
{
      global $wpdb;
      // this if statement makes sure that the table doe not exist already
      if($wpdb->get_var("show tables like my_table_name") != $eventtable) 
      {
            //For creating calendar table
            $eve_table = "CREATE TABLE $eventtable (
                        event_id INT(11) NOT NULL AUTO_INCREMENT ,
                        event_begin DATE NOT NULL ,
                        event_end DATE NOT NULL ,
                        event_title VARCHAR(30) NOT NULL ,
                        event_desc TEXT NOT NULL ,
                        event_venue VARCHAR(100) NOT NULL,
                        event_stime TIME ,
                        event_etime TIME ,
                        event_recur CHAR(1) ,
                        event_repeats INT(3) ,
                        event_author BIGINT(20) UNSIGNED ,
                        event_category BIGINT(20) UNSIGNED NOT NULL DEFAULT 1 ,
                        event_link TEXT ,
                        event_datecreated DATETIME NOT NULL,
                        event_status INT(1),
                        PRIMARY KEY (event_id));";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($eve_table);
            
            //For creating User table
            $user_table = "CREATE TABLE $usertable (
                  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                  `Eve_id` varchar(50), 
                  `Eve_User_Id` varchar(200),
                  `Accepted` int(1),
                  `Declined` int(1),
                  `Eve_Status` int(1),
                  PRIMARY KEY (id)
            );";
            dbDelta($user_table);
      }
}
// This hook will cause our creation function to run when the plugin is activated
register_activation_hook( __FILE__, 'event_manager' );

//For creating event
add_shortcode( 'cr_custom_registration', 'custom_registration_shortcode' ); 
function custom_registration_shortcode() 
{
      ob_start();
      custom_registration_function();
      return ob_get_clean();
}

//For edit event
add_shortcode( 'edit_eve', 'edit_eve_shortcode' );
function edit_eve_shortcode() 
{
      ob_start();
      edit_event();
      return ob_get_clean();
}

//For all event list
add_shortcode( 'all_eve_list', 'all_eve_list_shortcode' );
function all_eve_list_shortcode() 
{
      ob_start();
      all_event_list();
      return ob_get_clean();
}

//For Created by Me Event list
add_shortcode( 'created_eve', 'created_eve_shortcode' );
function created_eve_shortcode() 
{
      ob_start();
      created_event();
      return ob_get_clean();
}

//For all invitations
add_shortcode( 'all_invitaion_list', 'all_invitaion_list_shortcode' );
function all_invitaion_list_shortcode() 
{
      ob_start();
      all_invitaion_list();
      return ob_get_clean();
}

//For pending invitations
add_shortcode( 'pending_invitaion_list', 'pending_invitaion_list_shortcode' );
function pending_invitaion_list_shortcode() 
{
      ob_start();
      pending_invitaion_list();
      return ob_get_clean();
}

//For to be attended
add_shortcode( 'to_be_attended_list', 'to_be_attended_list_shortcode' );
function to_be_attended_list_shortcode() 
{
      ob_start();
      to_be_attended_list();
      return ob_get_clean();
}
//For dashboard calendar
add_shortcode( 'dashboard_calendar', 'dashboard_calendar1_shortcode' );
function dashboard_calendar1_shortcode() 
{
      ob_start();
      dashboard_calendar1();
      return ob_get_clean();
}

