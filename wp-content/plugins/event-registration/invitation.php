<?php
//Function for all invitation list
function all_invitaion_list()
{
      global $wpdb, $usertable, $eventtable;
      $event_id= 1;
      $current_user = wp_get_current_user();
      $cuser = $current_user->user_login;
      $cuid = $current_user->ID;
      
      $result = $wpdb->get_results( "SELECT * FROM ".$usertable." where Eve_User_Id='$cuid'"); 
      if(empty($result))
      {
            echo "No Invitations";
      }
      else
      {?>
<script>
        
    </script>
<?php
            echo '
                  <table class="table table-striped table-bordered table-hover"  id="data">
                        <thead>
                              <tr>
                                    <th>S.No</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Venue</th>
                                    <th>Start Date & Time</th>
                                    <th>End Date & Time</th>
                                    <th> My Response</th>
                              </tr>
                        </thead>
                        <tbody>';
            $result = $wpdb->get_results( "SELECT * FROM ".$usertable." where Eve_User_Id='$cuid'"); 
            foreach($result as $row)
            {
                  $accepted = $row->Accepted;
                  $declined = $row->Declined;
                  $eveid = $row->Eve_id;
                  $result2 = $wpdb->get_results("Select * from ".$eventtable." where event_id = '$eveid'");

                  foreach($result2 as $row2)
                  {
                        $start_time = date('h:ia', strtotime($row2->event_stime));
                        $end_time = date('h:ia', strtotime($row2->event_etime));
                        echo '
                        <tr  class="odd gradeX">
                              <td>'.$event_id++.'</td>
                              <td>'.$row2->event_title.'</td>
                              <td>'.$row2->event_desc.'</td>
                              <td>'.$row2->event_venue.'</td>
                              <td>Date: '.$row2->event_begin.'<br/>Time: '.$start_time.'</td>
                              <td>Date: '.$row2->event_end.'<br/>Time: '.$end_time.'</td>
                              <td class="center">';
                              if($accepted == 0 && $declined == 1){
                                    echo '<span class="label label-sm label-success">
                              Accepted </span>';
                              }else if($accepted == 1 && $declined == 0){
                                    echo '<span class="label label-sm label-warning">
                              Declined</span>';
                              }else {
                                    echo '<span class="label label-sm label-default">
                              Pending </span>';
                              }
                        echo '</td></tr>';
                  }
            }
            echo '</tbody>
</table>
';
      }
}

//Function to show pending invitation list
function pending_invitaion_list()
{
      global $wpdb, $eventtable, $usertable, $table_prefix;
      $current_user = wp_get_current_user();
      
      $uid=$_GET['uid'];
      
      if($_GET['status'] == "yes")
      {            
            $id = $_GET['id'];
            $data = array( 
                   'Accepted' => 0,
                  'Declined' => 1
            );
            $formats = array( '%d', '%d'); 
            $whe=array(
                  'Eve_id'=> $id,
                  'Eve_User_Id' => $uid
            );

            $wpdb->update($usertable, $data, $whe, $formats);
            
            wp_redirect(site_url().'/wp-content/plugins/event-registration/google-api/quickstart.php?uid='.$uid.'&eid='.$id);
            exit;
      }
      else if($_GET['status'] == "no")
      {
            //For inserting data in wp_event_reg
            $data = array( 
                  'Accepted' => 1,
                  'Declined' => 0
            );
            $formats = array( '%d', '%d'); 
            $whe=array(
                  'id'=> $_GET['id'],
                  'Eve_User_Id' => $_GET['uid']
            );
            //For inserting data in wp_event_users 
            $wpdb->update($usertable, $data, $whe, $formats);
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
            
            $result = $wpdb->get_results( "SELECT * FROM ".$usertable." where Eve_User_Id='$cuid' AND Accepted = '0' AND Declined = '0'"); 
            if(empty($result))
            {
                  echo "No Pending requests";
            }
            else
            {
                  echo '
                  <table class="table table-striped table-bordered table-hover"  id="data">
                  <thead>
                        <tr>
                              <th>S.No</th>
                              <th>Title</th>
                              <th>Description</th>
                              <th>Venue</th>
                              <th>Start Date & Time</th>
                              <th>End Date & Time</th>
                              <th>Response</th>
                        </tr>
                        </thead><tbody>';
                  foreach($result as $row)
                  {
                        $id = $row->id;
                        $eveid = $row->Eve_id;
                        $todays_date = date('Y-m-d');
                        $result2 = $wpdb->get_results("Select * from ".$eventtable." where event_id = '$eveid'");
                        //echo $wpdb->last_query;
                        //die;
                        foreach($result2 as $row2)
                        {
                              $start_time = date('h:ia', strtotime($row2->event_stime));
                              $end_time = date('h:ia', strtotime($row2->event_etime));
                              if($row2->event_begin>=$todays_date){
                              echo '
                              <tr  class="odd gradeX">
                                    <td>'.$i++.'</td>
                                    <td>'.$row2->event_title.'</td>
                                    <td>'.$row2->event_desc.'</td>
                                    <td>'.$row2->event_venue.'</td>
                                    <td>Date: '.$row2->event_begin.'<br/>Time: '.$start_time.'</td>
                                    <td>Date: '.$row2->event_end.'<br/>Time: '.$end_time.'</td>
                                    <td class="center"><a href="'.site_url().'/index.php/customer-area/pages/pending-invitations?status=yes&id='.$id.'&uid='.$cuid.'" class="links">Accepted</a>/<a href="'.site_url().'/index.php/customer-area/pages/pending-invitations?status=no&id='.$id.'&uid='.$cuid.'" class="links">Decline</a></td>
                              </tr>';}
                        }
                  }
                  echo 
                  '</tbody></table>';
            }
      }
}