<?php
/*
      Plugin Name: Custom Registration
      Plugin URI:
      Description: Updates user rating based on number of posts.
      Version: 1.0
      Author: 
      Author URI: 
*/
//Used for including js

$dir = plugin_dir_path( __FILE__ );
require_once($dir.'eve-reg-display.php');
require_once($dir.'event_list.php');
require_once($dir.'invitation.php');

//Used to create table for plugin when activated
function event_manager()
{
      // do NOT forget this global
      global $wpdb;
      $appointment = $wpdb->prefix.  'event_reg';
      $eveuser = $wpdb->prefix.  'event_users';
      // this if statement makes sure that the table doe not exist already
      if($wpdb->get_var("show tables like my_table_name") != $appointment) 
      {
            $sql = "CREATE TABLE $appointment (
            `Eve_id` int(9) NOT NULL AUTO_INCREMENT,
            `Eve_Title` varchar(50), 
            `Eve_Desc` varchar(200),
            `Eve_Venue` varchar(100),
            `Eve_Sdate` varchar(10),
            `Eve_Stime` varchar(10),
            `Eve_Tdate` varchar(10),
            `Eve_Ttime` varchar(10),
            `Eve_Author` varchar(20),
            `Eve_datecreated` varchar(10),
            `Eve_Status` int(1),
            UNIQUE KEY id (Eve_id)
            );";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            $sql = "CREATE TABLE $eveuser (
            `id` mediumint(9) NOT NULL AUTO_INCREMENT,
            `Eve_id` varchar(50), 
            `Eve_User_Id` varchar(200),
            `Accepted` int(1),
            `Declined` int(1),
            `Eve_Status` int(1),
            UNIQUE KEY id (id)
            );";
            dbDelta($sql);
      }
}
// this hook will cause our creation function to run when the plugin is activated
register_activation_hook( __FILE__, 'event_manager' );
//--------------------------------------------------------------------------------------------------

//Function to insert data in table
function complete_registration() 
{
      global $wpdb, $table_prefix;
      $current_user = wp_get_current_user();
      $cuser = $current_user->user_login;

      $cuid = $current_user->ID;
      $dc = date("Y-m-d");
      $status = 0;
      $active = 0;
      $decline = 0;
      $user = $_POST['traditional'];
      $user[count($user)] = $cuid;
      $u = implode(",", $user);

      $tablename =  $table_prefix . 'event_reg';
      $tablename1 =  $table_prefix . 'event_users';
      //For inserting data in wp_event_reg
      $data = array( 
            'Eve_Title' => $_POST['event_title'], 
            'Eve_Desc' => $_POST['desc'],
            'Eve_Venue' => $_POST['venue'], 
            'Eve_Sdate' => $_POST['start_date'],
            'Eve_Stime' => $_POST['start_time'], 
            'Eve_Tdate' => $_POST['to_date'], 
            'Eve_Ttime' => $_POST['to_time'], 
            'Eve_Author' => $cuser,
            'Eve_datecreated' => $dc,
            'Eve_Status' => $status
      );
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
      //For inserting data in wp_event_users 
      $wpdb->insert($tablename, $data, $formats);

      $eu =explode(",", $u);
      for($i=0;$i<count($eu);$i++)
      {
            $data1 = array( 
            'Eve_id' => $_POST['event_id'],
            'Eve_User_Id' => $eu[$i], 
            'Accepted' => $active,
            'Declined' => $decline, 
            'Eve_Status' => $status);
            $formats1 = array( 
            '%s',
            '%s',
            '%d',
            '%d',
            '%d'
            );
            $wpdb->insert($tablename1, $data1, $formats1);

      }
      
    $to      = 'rohit.gupta@infobeans.com';
    $subject = 'the subject';
    $message = 'hello';
    $headers = '';
    wp_mail($to, $subject, $message, $headers);
    wp_redirect(site_url().'/index.php/customer-area/events-lists/created-by-me/');
    exit;
}

//Function to update data
function  complete_editeve() 
{
      global $wpdb, $table_prefix;
      $current_user = wp_get_current_user();
      $cuser = $current_user->user_login;
      $dc = date("Y-m-d");
      $status = 0;
      $active = null;
      $decline = null;

      $user = $_POST['traditional'];
      $u = implode(",", (array)$user);

      $tablename =  $table_prefix . 'event_reg';
      $tablename1 =  $table_prefix . 'event_users';
      //For inserting data in wp_event_reg
      $data = array( 
            'Eve_id' => $_POST['event_id'],
            'Eve_Title' => $_POST['event_title'], 
            'Eve_Desc' => $_POST['desc'],
            'Eve_Venue' => $_POST['venue'], 
            'Eve_Sdate' => $_POST['start_date'],
            'Eve_Stime' => $_POST['start_time'], 
            'Eve_Tdate' => $_POST['to_date'], 
            'Eve_Ttime' => $_POST['to_time'], 
            'Eve_Author' => $cuser,
            'Eve_datecreated' => $dc,
            'Eve_Status' => $status
      );
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
      $whe=array(
            'Eve_id'=> $_POST['event_id']
      );
      //For inserting data in wp_event_users 
      $wpdb->update($tablename, $data, $whe, $formats);
      $eu =explode(",", $u);
      if($u != null)
      {
            for($i=0;$i<count($eu);$i++)
            {
                  $data1 = array( 
                        'Eve_id' => $_POST['event_id'],
                        'Eve_User_Id' => $eu[$i], 
                        'Accepted' => $active,
                        'Declined' => $decline, 
                        'Eve_Status' => $status
                  );

                  $formats1 = array( 
                        '%s',
                        '%s',
                        '%d',
                        '%d',
                        '%d'
                  ); 
                  $wpdb->insert($tablename1, $data1, $formats1);
            }
      }
      wp_redirect(site_url().'/index.php/customer-area/events-lists/created-by-me/');
      exit;
}


function custom_registration_function() 
{
      if ( isset($_POST['submit'] ) ) 
      {
            registration_validation(
                  $_POST['event_title'],
                  $_POST['start_date'],
                  $_POST['start_time'],
                  $_POST['to_date'],
                  $_POST['to_time'],
                  $_POST['traditional'],
                  $_POST['venue'],
                  $_POST['desc']
            );
            // sanitize user form input
            global $id, $title, $sdate, $stime, $tdate, $ttime, $venue, $users, $desc, $status;
            $id   =   $_POST['event_id'] ;
            $title   =   $_POST['event_title'] ;
            $sdate   =   $_POST['start_date'] ;
            $stime      = $_POST['start_time'];
            $tdate    = $_POST['to_date'] ;
            $ttime =   $_POST['to_time'];
            $venue  =   $_POST['venue'] ;
            $users   =   $_POST['traditional'] ;
            $desc        =   $_POST['desc'] ;
            $status = 0;
            // call @function complete_registration to create the user
            // only when no WP_error is found
            complete_registration(
                  $id,
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
            $eve_id,
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

function edit_event()
{
      if ( isset($_POST['submit'] ) ) 
      {
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

            // call @function complete_registration to create the user
            // only when no WP_error is found
            complete_editeve(
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
      edit_registration_form(
            $eve_title, 
            $eve_sdate, 
            $eve_stime, 
            $eve_tdate, 
            $eve_ttime, 
            $eve_venue, 
            $eve_users,
            $desc
      );
	
            wp_redirect(site_url().'/index.php/customer-area/events-lists/created-by-me/');
            exit;
      
}



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