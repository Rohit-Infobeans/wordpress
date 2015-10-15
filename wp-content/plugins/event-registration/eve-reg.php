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
      $appointment = $wpdb->prefix. 'calendar';
      $eveuser = $wpdb->prefix.  'event_users';
      // this if statement makes sure that the table doe not exist already
      if($wpdb->get_var("show tables like my_table_name") != $appointment) 
      {
            $sql = "CREATE TABLE $appointment (
                              event_id INT(11) NOT NULL AUTO_INCREMENT ,
                              event_begin DATE NOT NULL ,
                              event_end DATE NOT NULL ,
                              event_title VARCHAR(30) NOT NULL ,
                              event_desc TEXT NOT NULL ,
                              event_venue VARCHAR(100) NOT NULL,
                              event_time TIME ,
                              event_recur CHAR(1) ,
                              event_repeats INT(3) ,
                              event_author BIGINT(20) UNSIGNED ,
                              event_category BIGINT(20) UNSIGNED NOT NULL DEFAULT 1 ,
                              event_link TEXT ,
                              event_datecreated DATETIME NOT NULL,
                              event_status INT(1),
                              PRIMARY KEY (event_id)
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
                  PRIMARY KEY (id)
            );";
            dbDelta($sql);
      }
}
// this hook will cause our creation function to run when the plugin is activated
register_activation_hook( __FILE__, 'event_manager' );
//--------------------------------------------------------------------------------------------------

//Validations
function registration_validation( $eve_title, $eve_sdate, $eve_tdate, $eve_stime, $repeat, $recur, $eve_venue, $eve_users, $desc)  
{
      global $reg_errors;
      $reg_errors = new WP_Error;
      if ( empty( $eve_title ) || empty( $eve_sdate ) || empty( $eve_tdate ) || empty( $eve_venue ) || empty($eve_users)) 
      {
            $reg_errors->add('field', 'Required form field is missing');
      }
      if ( 3 > strlen( $eve_title ) ) 
      {
            $reg_errors->add( 'eventtitle_length', 'Event title too short' );
      }
     
      if ( is_wp_error( $reg_errors ) ) 
      {
            foreach ( $reg_errors->get_error_messages() as $error ) 
            {     
                    echo '<div style="color:red">';
                    echo '<strong>ERROR</strong>:';
                    echo $error . '<br/>';
                    echo '</div>';               
            }
      }
}

//Function to insert data in table
function complete_registration() 
{
      global $reg_errors;
      global $wpdb, $table_prefix;
      if ( 1 > count( $reg_errors->get_error_messages() ) ) 
      {  
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
            $eid = $_POST['event_id'];
            $etitle =  $_POST['event_title'];
            $edesc = $_POST['desc'];
            $evenue = $_POST['venue'];
            $esdate = $_POST['start_date'];
            $estime = $_POST['start_time'];
            $etdate = $_POST['to_date'];
            $ettime = $_POST['to_time'];
            $testtime = date("H:i", strtotime($estime));
            $tablename =  $table_prefix . 'calendar';
            $tablename1 =  $table_prefix . 'event_users';
            
            //For inserting data in wp_event_reg
            $data = array( 
                  'event_begin' => $_POST['start_date'],
                  'event_end' => $_POST['to_date'],
                  'event_title' => $_POST['event_title'], 
                  'event_desc' => $_POST['desc'],
                  'event_venue' => $_POST['venue'],
                  'event_time' => $testtime,
                  'event_recur'=>$_POST['event_recur'],
                  'event_repeats'=>$_POST['event_repeats'],
                  'event_author' => $cuid,
                  'event_category'=>null,
                  'event_link'=>null,
                  'event_datecreated' => date('Y-m-d H:i:s'),
                  'event_status' => $status
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

                  $result = $wpdb->get_row( "SELECT user_email FROM wp_users WHERE id='$eu[$i]'");
                  $to = $result->user_email;
                  $subject = 'Event Invitation';
                  $message = '		<div id="email_container" style="background:#444">
                        <div style="width:570px; padding:0 0 0 20px; margin:50px auto 12px auto" id="email_header">
                              <span style="background:#585858; color:#fff; padding:12px;font-family:trebuchet ms; letter-spacing:1px; 
                                    -moz-border-radius-topleft:5px; -webkit-border-top-left-radius:5px; 
                                    border-top-left-radius:5px;moz-border-radius-topright:5px; -webkit-border-top-right-radius:5px; 
                                    border-top-right-radius:5px;">
                                    Event Invitation
                              </div>
                        </div>
                  
                  
                        <div style="width:550px; padding:0 20px 20px 20px; background:#fff; margin:0 auto; border:3px #000 solid;
                              moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; color:#454545;line-height:1.5em; " id="email_content">
                              
                              <h1 style="padding:5px 0 0 0; font-family:georgia;font-weight:500;font-size:24px;color:#000;border-bottom:1px solid #bbb">
                                    This is the invitaion for '.$etitle.'
                              </h1>
                              
                              <p>
                                    This event is organized by '.$cuser.'. Related informations <br/>Venue: '
                                    .$evenue.'<br/>Event Starts at: '.$esdate.' '.$estime.'<br/>Event ends at: '.$etdate.' '.$ettime.'<br/>Event description: '.$edesc.'
                              </p>
                              <p>
                                    <a href="'.site_url().'/index.php/customer-area/pages/pending-invitations?status=yes&id='.$eid.'&uid='.$cuid.'"><span class="myButton" id="myButton" style="box-shadow: rgb(207, 134, 108) 0px 1px 0px 0px inset; border-radius: 3px; border: 1px solid rgb(148, 41, 17); display: inline-block; cursor: pointer; color: rgb(255, 255, 255); font-family: Arial; font-size: 13px; padding: 6px 24px; text-decoration: none; text-shadow: rgb(133, 70, 41) 0px 1px 0px; background: linear-gradient(rgb(208, 69, 27) 5%, rgb(188, 51, 21) 100%) rgb(208, 69, 27);">Accept</span></a>
                                     
                                    <a href="'.site_url().'/index.php/customer-area/pages/pending-invitations?status=no&id='.$eid.'&uid='.$cuid.'"><span class="myButton" id="myButton" style="box-shadow: rgb(207, 134, 108) 0px 1px 0px 0px inset; border-radius: 3px; border: 1px solid rgb(148, 41, 17); display: inline-block; cursor: pointer; color: rgb(255, 255, 255); font-family: Arial; font-size: 13px; padding: 6px 24px; text-decoration: none; text-shadow: rgb(133, 70, 41) 0px 1px 0px; background: linear-gradient(rgb(208, 69, 27) 5%, rgb(188, 51, 21) 100%) rgb(208, 69, 27);">Decline</span></a>
                              </p> 				
                        </div>
                  </div>';
                  $headers= "MIME-Version: 1.0\n" ."Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
                  wp_mail($to, $subject, $message, $headers);
            }
            wp_redirect(site_url().'/index.php/customer-area/events-lists/created-by-me/');
            exit;
      }
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
      $etitle =  $_POST['event_title'];
      $edesc = $_POST['desc'];
      $evenue = $_POST['venue'];
      $esdate = $_POST['start_date'];
      $estime = $_POST['start_time'];
      $etdate = $_POST['to_date'];
      $ettime = $_POST['to_time'];   
      $tablename =  $table_prefix . 'calendar';
      $tablename1 =  $table_prefix . 'event_users';
      //For inserting data in wp_event_reg
      $data = array( 
                  'event_begin' => $_POST['start_date'],
                  'event_end' => $_POST['to_date'],
                  'event_title' => $_POST['event_title'], 
                  'event_desc' => $_POST['desc'],
                  'event_venue' => $_POST['venue'],
                  'event_time' => $_POST['start_time'],
                  'event_recur'=>$_POST['event_recur'],
                  'event_repeats'=>$_POST['event_repeats'],
                  'event_author' => $cuser,
                  'event_category'=>null,
                  'event_link'=>null,
                  'event_datecreated' => date( 'Y-m-d h:i:s'),
                  'event_status' => $status
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
                  '%s',
                  '%d'
      ); 
      $whe=array(
            'event_id'=> $_POST['event_id']
      );
      //For inserting data in wp_event_users 
      $wpdb->update($tablename, $data, $whe, $formats);
      $eu =explode(",", $u);
      $eid = $_POST['event_id'];
      if($u != null)
      {
            for($i=0;$i<count($eu);$i++)
            {
                  $result1 = $wpdb->get_results("SELECT * FROM".$table_prefix."event_users WHERE Eve_id ='$eid' AND Eve_User_Id='$eu[$i]'");
                  if(count($result1) == 0)
                  {
                        $data1 = array( 
                        'Eve_id' => $eid,
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
                  else
                  {
                        exit;
                  }
                  
            }
      }
      $eid = $_POST['event_id'];
      $result = $wpdb->get_results( "SELECT * FROM wp_event_users WHERE Eve_id ='$eid'");

      foreach($result as $row)
      {
            $uid = $row->Eve_User_Id;
            $result1 = $wpdb->get_row( "SELECT user_email FROM wp_users WHERE ID ='$uid'");
            $to = $result1->user_email;
            $subject = 'Event Updates';
            $message = '<div id="email_container" style="background:#444">
			<div style="width:570px; padding:0 0 0 20px; margin:50px auto 12px auto" id="email_header">
				<span style="background:#585858; color:#fff; padding:12px;font-family:trebuchet ms; letter-spacing:1px; 
					-moz-border-radius-topleft:5px; -webkit-border-top-left-radius:5px; 
					border-top-left-radius:5px;moz-border-radius-topright:5px; -webkit-border-top-right-radius:5px; 
					border-top-right-radius:5px;">
					Updation in Event Information
				</div>
			</div>
		
		
			<div style="width:550px; padding:0 20px 20px 20px; background:#fff; margin:0 auto; border:3px #000 solid;
				moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; color:#454545;line-height:1.5em; " id="email_content">
				
				<h1 style="padding:5px 0 0 0; font-family:georgia;font-weight:500;font-size:24px;color:#000;border-bottom:1px solid #bbb">
					 '.$etitle.' Information updated
				</h1>
				
				<p>
					This event is organized by '.$cuser.'. Updation in informations <br/>Venue: '
                              .$evenue.'<br/>Event Starts at: '.$esdate.' '.$estime.'<br/>Event ends at: '.$etdate.' '.$ettime.'<br/>Event description: '.$edesc.'
				</p>
				<p>
                              <a href="'.site_url().'/index.php/customer-area/pages/pending-invitations?status=yes&id='.$eid.'&uid='.$cuid.'"><span class="myButton" id="myButton" style="box-shadow: rgb(207, 134, 108) 0px 1px 0px 0px inset; border-radius: 3px; border: 1px solid rgb(148, 41, 17); display: inline-block; cursor: pointer; color: rgb(255, 255, 255); font-family: Arial; font-size: 13px; padding: 6px 24px; text-decoration: none; text-shadow: rgb(133, 70, 41) 0px 1px 0px; background: linear-gradient(rgb(208, 69, 27) 5%, rgb(188, 51, 21) 100%) rgb(208, 69, 27);">Accept</span></a>
                               
					<a href="'.site_url().'/index.php/customer-area/pages/pending-invitations?status=no&id='.$eid.'&uid='.$cuid.'"><span class="myButton" id="myButton" style="box-shadow: rgb(207, 134, 108) 0px 1px 0px 0px inset; border-radius: 3px; border: 1px solid rgb(148, 41, 17); display: inline-block; cursor: pointer; color: rgb(255, 255, 255); font-family: Arial; font-size: 13px; padding: 6px 24px; text-decoration: none; text-shadow: rgb(133, 70, 41) 0px 1px 0px; background: linear-gradient(rgb(208, 69, 27) 5%, rgb(188, 51, 21) 100%) rgb(208, 69, 27);">Decline</span></a>
				</p> 				
			</div>
		</div>';
            $headers= "MIME-Version: 1.0\n" ."Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
            wp_mail($to, $subject, $message, $headers);
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
                  $_POST['event_repeats'],
                  $_POST['event_recur'],
                  $_POST['venue'],
                  $_POST['traditional'],
                  $_POST['desc']
            );

            global $title, $sdate, $stime, $tdate, $repeat, $recur, $venue, $users, $desc, $status;
            $title   =   $_POST['event_title'] ;
            $sdate   =   $_POST['start_date'] ;
            $stime      = $_POST['start_time'];
            $tdate    = $_POST['to_date'] ;
            $repeat = $_POST['event_repeats'];
            $recur = $_POST['event_recur'];
            $venue  =   $_POST['venue'] ;
            $users   =   $_POST['traditional'] ;
            $desc        =   $_POST['desc'] ;
            $status = 0;

            complete_registration(
                  $title,
                  $sdate,
                  $tdate,
                  $stime,
                  $repeat,
                  $recur,
                  $venue,
                  $users,
                  $desc,
                  $status
            );
      }
      registration_form(
            $eve_title, 
            $eve_sdate,
            $eve_tdate,
            $eve_stime,
            $repeat,
            $recur,
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
                  $_POST['event_repeats'],
                  $_POST['event_recur'],
                  $_POST['venue'],
                  $_POST['traditional'],
                  $_POST['desc']
            );

            global $title, $sdate, $stime, $tdate, $repeat, $recur, $venue, $users, $desc, $status;
            $title   =   $_POST['event_title'] ;
            $sdate   =   $_POST['start_date'] ;
            $stime      = $_POST['start_time'];
            $tdate    = $_POST['to_date'] ;
            $repeat = $_POST['event_repeats'];
            $recur = $_POST['event_recur'];
            $venue  =   $_POST['venue'] ;
            $users   =   $_POST['traditional'] ;
            $desc        =   $_POST['desc'] ;
            $status = 0;

            complete_editeve(
                  $title,
                  $sdate,
                  $stime,
                  $tdate,
                  $repeat,
                  $recur,
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
            $repeat,
            $recur,
            $eve_venue, 
            $eve_users,
            $desc
      );	
 
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
