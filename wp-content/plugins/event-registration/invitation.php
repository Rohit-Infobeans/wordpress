<?php
//Function for all invitation list
function all_invitaion_list()
{
      global $wpdb, $table_prefix;
      $i = 1;
      $current_user = wp_get_current_user();
      $cuser = $current_user->user_login;
      $cuid = $current_user->ID;
      $result = $wpdb->get_results( "SELECT * FROM ".$table_prefix."event_users where Eve_User_Id='$cuid'"); 
      if(empty($result))
      {
            echo "No Invitations";
      }
      else
      {
            echo '
                  <table>
                        <tr>
                              <th>S.No</th>
                              <th>Title</th>
                              <th>Description</th>
                              <th>Venue</th>
                              <th>Start Date & Time</th>
                              <th>End Date & Time</th>
                              <th>Organizer</th>
                              <th> My Response</th>
                        </tr>';

            $result1 = $wpdb->get_row( "SELECT * FROM ".$table_prefix."users where user_login='$cuser'");
            $cu1 =  $result1->user_email;
            $cuid = $result1->ID;

            $result = $wpdb->get_results( "SELECT * FROM ".$table_prefix."event_users where Eve_User_Id='$cuid'"); 
            foreach($result as $row)
            {
                  $accepted = $row->Accepted;
                  $declined = $row->Declined;
                  $eveid = $row->Eve_id;
                  $result2 = $wpdb->get_results("Select * from ".$table_prefix."calendar where event_id = '$eveid'");
                  foreach($result2 as $row2)
                  {
                        echo '
                        <tr>
                              <td>'.$i++.'</td>
                              <td>'.$row2->event_title.'</td>
                              <td>'.$row2->event_desc.'</td>
                              <td>'.$row2->event_venue.'</td>
                              <td>'.$row2->event_begin.' '.$row2->event_time.'</td>
                              <td>'.$row2->event_end.'</td>
                              <td>';
                              $aid = $row2->event_author;
                              $res = $wpdb->get_row("Select * from ".$table_prefix."users where ID='$aid'");
                              echo $res->display_name;
                              
                              echo '</td>';
                              if($accepted == 0 && $declined == 1)
                              {
                                    echo '<td>Accepted</td>';
                              }
                                    else if($accepted == 1 && $declined == 0)
                              {
                                    echo '<td>Declined</td>';
                              }
                              else
                              {
                                    echo '<td>Pending</td>';
                              }
                        echo '</tr>';
                              }
                  }
                  echo '</table>';
      }
}

//Function to show pending invitation list
function pending_invitaion_list()
{
      global $wpdb, $table_prefix;
      $current_user = wp_get_current_user();
      $uid=$_GET['uid'];
      if($_GET['status'] == "yes")
      {            
            $tablename1 =  $table_prefix . 'event_users';

            $data = array( 
                   'Accepted' => 0,
                  'Declined' => 1
            );
            $formats = array( 
                  '%d',
                  '%d'
            ); 
            $whe=array(
                  'id'=> $_GET['id'],
                  'Eve_User_Id' => $uid
            );

            $wpdb->update($tablename1, $data, $whe, $formats);
            $res = $wpdb->get_row("Select * from ".$table_prefix."users where ID='$uid'");
            $to = $res->user_email;
            $subject = $res->display_name.' accepted your invitation';
            $message = '
            <div id="email_container" style="background:#444">
                  <div style="width:570px; padding:0 0 0 20px; margin:50px auto 12px auto" id="email_header">
                        <span style="background:#585858; color:#fff; padding:12px;font-family:trebuchet ms; letter-spacing:1px; 
                        -moz-border-radius-topleft:5px; -webkit-border-top-left-radius:5px; 
                        border-top-left-radius:5px;moz-border-radius-topright:5px; -webkit-border-top-right-radius:5px; 
                        border-top-right-radius:5px;">
                        Invitation Accepted</div>
                  </div>
                  <div style="width:550px; padding:0 20px 20px 20px; background:#fff; margin:0 auto; border:3px #000 solid;
                        moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; color:#454545;line-height:1.5em; " id="email_content">

                        <h1 style="padding:5px 0 0 0; font-family:georgia;font-weight:500;font-size:24px;color:#000;border-bottom:1px solid #bbb">
                              '.$res->display_name.' accepted invitation
                        </h1>

                        <p>
                              '.$res->display_name.' will be attending the event organised by you.
                        </p>
                                                
                  </div>
            </div>';
            $headers= "MIME-Version: 1.0\n" ."Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
            wp_mail($to, $subject, $message, $headers);
            wp_redirect(site_url().'/index.php/customer-area/events-lists/to-be-attended/');
            exit;
      }
      else if($_GET['status'] == "no")
      {
            $tablename1 =  $table_prefix . 'event_users';
            //For inserting data in wp_event_reg
            $data = array( 
                  'Accepted' => 1,
                  'Declined' => 0
            );
            $formats = array( 
                  '%d',
                  '%d'
            ); 
            $whe=array(
                  'id'=> $_GET['id'],
                  'Eve_User_Id' => $_GET['uid']
            );
            //For inserting data in wp_event_users 
            $wpdb->update($tablename1, $data, $whe, $formats);
            wp_redirect(site_url().'/index.php/customer-area/dashboard/');
            exit;
      }
      else
      {
            $cuser = $current_user->user_login;
            $i = 1;
            $result1 = $wpdb->get_row( "SELECT * FROM wp_users where user_login='$cuser'");
            $cu1 =  $result1->user_email;
            $cuid = $result1->ID;
            
            $result = $wpdb->get_results( "SELECT * FROM ".$table_prefix."event_users where Eve_User_Id='$cuid' AND Accepted = '0' AND Declined = '0'"); 
            if(empty($result))
            {
                  echo "No Pending requests";
            }
            else
            {
                  echo '
                  <table>
                        <tr>
                        <th>S.No</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Venue</th>
                        <th>Start Date & Time</th>
                        <th>End Date & Time</th>
                        <th>Response</th>
                        </tr>';
                  foreach($result as $row)
                  {
                        $id = $row->id;
                        $eveid = $row->Eve_id;
                        $result2 = $wpdb->get_results("Select * from ".$table_prefix."calendar where event_id = '$eveid'");
                        foreach($result2 as $row2)
                        {
                              echo '
                              <tr>
                                    <td>'.$i++.'</td>
                                    <td>'.$row2->event_title.'</td>
                                    <td>'.$row2->event_desc.'</td>
                                    <td>'.$row2->event_venue.'</td>
                                    <td>'.$row2->event_begin.'</td>
                                    <td>'.$row2->event_end.'</td>
                                    <td><a href="'.site_url().'/index.php/customer-area/pages/pending-invitations?status=yes&id='.$id.'&uid='.$cuid.'">Accepted</a>/<a href="'.site_url().'/index.php/customer-area/pages/pending-invitations?status=no&id='.$id.'&uid='.$cuid.'">Decline</a></td>
                              </tr>';
                        }
                  }
                  echo '</table>';
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
            echo "No Pending Requests";
      }
      else
      {
            echo '
            <table>
                  <tr>
                  <th>S.No</th>
                  <th>Title</th>
                  <th>Description</th>
                  <th>Venue</th>
                  <th>Start Date & Time</th>
                  <th>End Date & Time</th>
                  </tr>';
            foreach($result as $row)
            {
                  $id = $row->id;
                  $eveid = $row->Eve_id;
                  $result2 = $wpdb->get_results("Select * from ".$table_prefix."calendar where event_id = '$eveid' AND event_status='0'");
                  foreach($result2 as $row2)
                  {
                  echo '
                  <tr>
                        <td>'.$i++.'</td>
                        <td>'.$row2->event_title.'</td>
                        <td>'.$row2->event_desc.'</td>
                        <td>'.$row2->event_venue.'</td>
                        <td>'.$row2->event_begin.' '.$row2->event_time.'</td>
                        <td>'.$row2->event_end.'</td>
                  </tr>';
                  }
            }
            echo '</table>';
      }
}