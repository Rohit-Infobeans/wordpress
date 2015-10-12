<?php
//Function for all invitation list
function all_invitaion_list()
{
      global $wpdb, $table_prefix;
      $i = 1;
      $current_user = wp_get_current_user();
      $cuser = $current_user->user_login;
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
                              <td>'.$row2->event_author.'</td>';
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
                  'Eve_User_Id' => $_GET['uid']
            );

            $wpdb->update($tablename1, $data, $whe, $formats);
            
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
                  echo "Please see Pending requests";
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