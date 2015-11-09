<?php
function all_event_list()
{
      global $wpdb,$table_prefix;
      $table_prefix = $wpdb->prefix;
      $i = 1;
      $current_user = wp_get_current_user();
      $cuser = $current_user->user_login;
      $cuid = $current_user->ID;
      $result = $wpdb->get_results( "SELECT * FROM ".$table_prefix."event_users where Eve_User_Id='$cuid'"); 
      if(empty($result))
      {
            echo "No invitations";
      }
      else
      {
             echo '
            <table class="table table-striped table-bordered table-hover" id="data">
                  <thead>
                        <tr>
                              <th>S.No</th>
                              <th>Title</th>
                              <th>Description</th>
                              <th>Venue</th>
                              <th>Start Date & Time</th>
                              <th>End Date & Time</th>
                              <th>Author</th>
                        </tr>
                  </thead>
                  <tbody>';
            $result1 = $wpdb->get_row( "SELECT * FROM ".$table_prefix."users where user_login='$cuser'");
            $cu1 =  $result1->user_email;
            $cuid = $result1->ID;

            $result = $wpdb->get_results( "SELECT * FROM ".$table_prefix."event_users where Eve_User_Id='$cuid'"); 

            foreach($result as $row)
            {
                  $eveid = $row->Eve_id;
                  $result2 = $wpdb->get_results("Select * from ".$table_prefix."calendar where event_id = '$eveid'");
                  foreach($result2 as $row2)
                  {
                        $start_time = date('h:ia', strtotime($row2->event_stime));
                        $end_time = date('h:ia', strtotime($row2->event_etime));
                        echo '
                        <tr   class="odd gradeX">
                              <td>'.$i++.'</td>
                              <td>'.$row2->event_title.'</td>
                              <td>'.$row2->event_desc.'</td>
                              <td>'.$row2->event_venue.'</td>
                              <td>Date: '.$row2->event_begin.'<br/>Time: '.$start_time.'</td>
                              <td>Date: '.$row2->event_end.'<br/>Time: '.$end_time.'</td>
                              <td>';
                              $aid  = $row2->event_author;
                              $res = $wpdb->get_row("Select * from wp_users where ID= '$aid'");
                              echo $res->display_name;
                              echo '</td>
                        </tr>';
                  }
            }
            echo '
            </tbody>
      </table>';
      }
}

function created_event()
{
      global $wpdb, $table_prefix;
      if($_GET['status']=='added')
      {
            echo '
            <script>
                  alert("Event added toy your google calendar");
            </script>
            ';
      }
      else if($_GET['status']=='not')
      {
            echo '
            <script>
                  alert("Event not available");
            </script>
            ';
      }
      if($_GET['action']=='delete')
      {

            $eid = $_GET['id'];
            $tablename1 =  $table_prefix . 'calendar';
            $data = array( 
                  'event_status' => 1
            );
            $formats = array( 
                  '%d'
            ); 
            $whe=array(
                  'event_id'=> $eid
            );
            
            //For inserting data in wp_event_users 
            $wpdb->update($tablename1, $data, $whe, $formats);
            $result = $wpdb->get_results( "SELECT * FROM wp_event_users WHERE Eve_id ='$eid'");

            foreach($result as $row)
            {
                  $uid = $row->Eve_User_Id;
                  $result1 = $wpdb->get_row( "SELECT user_email FROM wp_users WHERE ID ='$uid'");
                  $bnfw = BNFW::factory();
                  if ($bnfw->notifier->notification_exists('delete-event'))
                  {
                        $notifications = $bnfw->notifier->get_notifications('delete-event');
                        foreach ($notifications as $notification) 
                        {
                              $setting = $bnfw->notifier->read_settings($notification->ID);
                              $to = $result->user_email;
                              $message  = 'Event has been cancelled';
                              $subject = $setting['subject'];
                              $headers= "MIME-Version: 1.0\n" ."Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
                              wp_mail( $to,  $subject , wpautop( $message  ), $headers );
                        } 
                  }
            }
            wp_redirect(site_url().'/index.php/customer-area/events-lists/created-by-me/');
            exit;                  
      }
      else
      {
            $current_user = wp_get_current_user();
            $cuid = $current_user->ID;
            $result = $wpdb->get_results( "SELECT * FROM ".$table_prefix."calendar where event_author='$cuid' AND event_status='0' ORDER BY event_datecreated DESC"); 
            $i = 1;
            if(empty($result))
            {
                  echo "No events created by you";
            }
            else
            {
                  echo '
                  <table class="table table-striped table-bordered table-hover" id="data">
                        <thead>
                              <tr>
                                    <th>S.No</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Venue</th>
                                    <th>Start Date & Time</th>
                                    <th>End Date & Time</th>
                                    <th>Author</th>
                                    <th>Created On</th>
                                    <th>Edit/Delete</th>
                              </tr>
                        </thead>
                        </tbody>';
                  foreach($result as $row)
                  {
                        $start_time = date('h:ia', strtotime($row->event_stime));
                        $end_time = date('h:ia', strtotime($row->event_etime));
                        $id = $row->event_id;
                        echo '
                              <tr class="odd gradeX">
                                    <td>'.$i++.'</td>
                                    <td>'.$row->event_title.'</td>
                                    <td>'.$row->event_desc.'</td>
                                    <td>'.$row->event_venue.'</td>
                                    <td>Date: '.$row->event_begin.'<br/>Time: '.$start_time.'</td>
                                    <td>Date: '.$row->event_end.'<br/>Time: '.$end_time.'</td>
                                    <td>';
                                    $aid = $row->event_author;
                                    $res = $wpdb->get_row("Select * from ".$table_prefix."users where ID='$aid'");
                                    echo $res->display_name. '</td>
                                    <td>'.$row->event_datecreated.'</td>
                                    <td><a href="'.site_url().'/index.php/customer-area/edit-event?eid='.$id.'">Edit</a>/<a href="'.site_url().'/index.php/customer-area/events-lists/created-by-me?action=delete&id='.$id.'">Delete</a></td>
                              </tr>';
                  }
                  echo '</tbody></table>';
            }
      }
}

function to_be_attended_list()
{
      global $wpdb, $table_prefix;
      $current_user = wp_get_current_user();
      $cuser = $current_user->ID;
      $i = 1;
      
      $result = $wpdb->get_results( "SELECT * FROM ".$table_prefix."event_users where Eve_User_Id='$cuser' AND Accepted = '0' AND Declined = '1'"); 
      
      if(empty($result))
      {
            echo "No Events to attend";
      }
      else
      {
            echo '
            <table class="table table-striped table-bordered table-hover" id="data">
                  <thead>
                        <tr>
                        <th>S.No</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Venue</th>
                        <th>Start Date & Time</th>
                        <th>End Date & Time</th>
                        </tr>
                  </thead>
                  <tbody>';
            foreach($result as $row)
            {
                  $id = $row->id;
                  $eveid = $row->Eve_id;
                  $todays_date = date('Y-m-d');
                  $result2 = $wpdb->get_results("Select * from ".$table_prefix."calendar where event_id = '$eveid' AND event_status='0'");
                  foreach($result2 as $row2)
                  {
                        $start_time = date('h:ia', strtotime($row2->event_stime));
                        $end_time = date('h:ia', strtotime($row2->event_etime));
                        if($row2->event_begin>=$todays_date)
                        {
                              echo '
                              <tr class="odd gradeX">
                                    <td>'.$i++.'</td>
                                    <td>'.$row2->event_title.'</td>
                                    <td>'.$row2->event_desc.'</td>
                                    <td>'.$row2->event_venue.'</td>
                                    <td>Date: '.$row2->event_begin.'<br/>Time: '.$start_time.'</td>
                                    <td>Date: '.$row2->event_end.'<br/>Time: '.$end_time.'</td>
                              </tr>';
                        }
                  }
            }
            echo '</tbody>
            </table>';
      }
}