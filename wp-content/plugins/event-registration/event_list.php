<?php
function all_event_list()
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
            <th>Author</th>
            </tr>';
      $result1 = $wpdb->get_row( "SELECT * FROM wp_users where user_login='$cuser'");
      $cu1 =  $result1->user_email;
      $cuid = $result1->ID;

      $result = $wpdb->get_results( "SELECT * FROM wp_event_users where Eve_User_Id='$cuid'"); 

      foreach($result as $row)
      {
            $eveid = $row->Eve_id;
            $result2 = $wpdb->get_results("Select * from wp_event_reg where Eve_id = '$eveid'");
            foreach($result2 as $row2)
            {
            echo '<tr>
                  <td>'.$i++.'</td>
                  <td>'.$row2->Eve_Title.'</td>
                  <td>'.$row2->Eve_Desc.'</td>
                  <td>'.$row2->Eve_Venue.'</td>
                  <td>'.$row2->Eve_Sdate.'</td>
                  <td>'.$row2->Eve_Tdate.'</td>
                  <td>'.$row2->Eve_Author.'</td>
            </tr>';
            }
      }
      echo '</table>';
}


function created_event()
{
      global $wpdb, $table_prefix;
      if($_GET['action']=='delete')
      {
            $tablename1 =  $table_prefix . 'event_reg';
            $data = array( 
                  'Eve_Status' => 1
            );
            $formats = array( 
                  '%d'
            ); 
            $whe=array(
                  'Eve_id'=> $_GET['id']
            );
            
            //For inserting data in wp_event_users 
            $wpdb->update($tablename1, $data, $whe, $formats);

            wp_redirect(site_url().'/index.php/customer-area/events-lists/created-by-me/');
            exit;
      }
      else
      {
            $current_user = wp_get_current_user();
            $cuser = $current_user->user_login;
            $result = $wpdb->get_results( "SELECT * FROM wp_event_reg where Eve_Author='$cuser' AND Eve_Status='0'"); 
            $i = 1;
            if(empty($result))
            {
                  echo "No events created by you";
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
                              <th>Author</th>
                              <th>Edit/Delete</th>
                        </tr>';
                  
                  foreach($result as $row)
                  {
                        $id = $row->Eve_id;
                        
                        echo '
                              <tr>
                                    <td>'.$i++.'</td>
                                    <td>'.$row->Eve_Title.'</td>
                                    <td>'.$row->Eve_Desc.'</td>
                                    <td>'.$row->Eve_Venue.'</td>
                                    <td>'.$row->Eve_Sdate.'</td>
                                    <td>'.$row->Eve_Tdate.'</td>
                                    <td>'.$row->Eve_Author.'</td>
                                    <td><a href="http://localhost/wordpress/?p=43&eid='.$row->Eve_id.'">Edit</a>/<a href="'.site_url().'/index.php/customer-area/events-lists/created-by-me?action=delete&id='.$id.'">Delete</a></td>
                              </tr>';

                  }
                  echo '</table>';
            }
      }
}