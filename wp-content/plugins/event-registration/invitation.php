<?php
//Function for all invitation list
function all_invitaion_list()
{
      global $wpdb;
      $i = 1;
      $current_user = wp_get_current_user();
      $cuser = $current_user->user_login;
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
      
      $result1 = $wpdb->get_row( "SELECT * FROM wp_users where user_login='$cuser'");
      $cu1 =  $result1->user_email;
      $cuid = $result1->ID;

      $result = $wpdb->get_results( "SELECT * FROM wp_event_users where Eve_User_Id='$cuid'"); 
      foreach($result as $row)
      {
            $accepted = $row->Accepted;
            $declined = $row->Declined;
            $eveid = $row->Eve_id;
            $result2 = $wpdb->get_results("Select * from wp_event_reg where Eve_id = '$eveid'");
            foreach($result2 as $row2)
            {
                  echo '
                  <tr>
                        <td>'.$i++.'</td>
                        <td>'.$row2->Eve_Title.'</td>
                        <td>'.$row2->Eve_Desc.'</td>
                        <td>'.$row2->Eve_Venue.'</td>
                        <td>'.$row2->Eve_Sdate.'</td>
                        <td>'.$row2->Eve_Tdate.'</td>
                        <td>'.$row2->Eve_Author.'</td>';
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

//Function to show pending invitation list
function pending_invitaion_list()
{
      global $wpdb, $table_prefix;
      $current_user = wp_get_current_user();
      if($_GET['status'] == "yes")
      {            
            $tablename1 =  $table_prefix . 'event_users';
            //For inserting data in wp_event_reg
            $data = array( 
                  'Declined' => 1
            );
            $formats = array( 
                  '%d'
            ); 
            $whe=array(
                  'id'=> $_GET['id'],
                  'Eve_User_Id' => $_GET['uid']
            );
            //For inserting data in wp_event_users 
            $wpdb->update($tablename1, $data, $whe, $formats);
            wp_redirect(site_url().'/index.php/customer-area/events-lists/to-be-attended/');
            exit;
      }
      else if($_GET['status'] == "no")
      {
            $tablename1 =  $table_prefix . 'event_users';
            //For inserting data in wp_event_reg
            $data = array( 
                  'Accepted' => 1
            );
            $formats = array( 
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
                        <th>Response</th>
                        </tr>';
                  foreach($result as $row)
                  {
                        $id = $row->id;
                        $eveid = $row->Eve_id;
                        $result2 = $wpdb->get_results("Select * from wp_event_reg where Eve_id = '$eveid'");
                        foreach($result2 as $row2)
                        {
                              echo '
                              <tr>
                                    <td>'.$i++.'</td>
                                    <td>'.$row2->Eve_Title.'</td>
                                    <td>'.$row2->Eve_Desc.'</td>
                                    <td>'.$row2->Eve_Venue.'</td>
                                    <td>'.$row2->Eve_Sdate.'</td>
                                    <td>'.$row2->Eve_Tdate.'</td>
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
                  $result2 = $wpdb->get_results("Select * from wp_event_reg where Eve_id = '$eveid'");
                  foreach($result2 as $row2)
                  {
                  echo '
                  <tr>
                        <td>'.$i++.'</td>
                        <td>'.$row2->Eve_Title.'</td>
                        <td>'.$row2->Eve_Desc.'</td>
                        <td>'.$row2->Eve_Venue.'</td>
                        <td>'.$row2->Eve_Sdate.'</td>
                        <td>'.$row2->Eve_Tdate.'</td>
                  </tr>';
                  }
            }
            echo '</table>';
      }
}