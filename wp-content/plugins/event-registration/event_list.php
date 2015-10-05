<?php
 function all_event_list()
 {
       $current_user = wp_get_current_user();
    $cuser = $current_user->user_login;
    
    echo '<table>
    <tr>
      <th>S.No</th>
      <th>Title</th>
      <th>Description</th>
      <th>Venue</th>
      <th>Start Date & Time</th>
      <th>End Date & Time</th>
      <th>Author</th>
    </tr>';
      global $wpdb;
      $i = 0;
      /* wpdb class should not be called directly.global $wpdb variable is an instantiation of the class already set up to talk to the WordPress database */ 
      $result1 = $wpdb->get_results( "SELECT * FROM wp_users where user_login=%s", $cuser);
      $result = $wpdb->get_results( "SELECT * FROM wp_event_reg"); /*mulitple row results can be pulled from the database with get_results function and outputs an object which is stored in $result */
      foreach($result1 as $row1)
      {
           echo $row1->user_email;
      }
      
      foreach($result as $row)
      {
            $cu = $row->Eve_Users;
            $sear = explode(",", $cu);
            if (in_array($cu1, $sear))
            {
                  echo '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->Eve_Title.'</td>
                        <td>'.$row->Eve_Desc.'</td>
                        <td>'.$row->Eve_Venue.'</td>
                        <td>'.$row->Eve_Sdate.'</td>
                        <td>'.$row->Eve_Tdate.'</td>
                        <td>'.$row->Eve_Author.'</td>
                  </tr>';
            }
      }
    echo '</table>';
}