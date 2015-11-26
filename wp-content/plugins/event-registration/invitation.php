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
			$resu = $wpdb->get_row("Select user_email from wp_users where ID='$uid'");

			$user_email = $resu->user_email;

			$mail_server = explode("@", $user_email);
			if($mail_server[1] == 'gmail.com')
			{
				wp_redirect(site_url().'/wp-content/plugins/event-registration/google-api/quickstart.php?uid='.$uid.'&eid='.$id.'&code=');
				exit;
			}
			else
			{

$res = $wpdb->get_row("Select * ,CONCAT(`event_begin`,'T',`event_stime`) as start, CONCAT(`event_end`,'T',`event_etime`) as end from wp_calendar WHERE event_id='$id'");
$e_author=$res->event_author;
$date = str_replace('-', '', $res->start);
$dtime = str_replace(':', '', $date);
$date1 = str_replace('-', '', $res->end);
$dtime1 = str_replace(':', '', $date1);
$result=$wpdb->get_row("select * from wp_users WHERE ID='$e_author'");
$message='BEGIN:VCALENDAR
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:REQUEST
BEGIN:VEVENT
DTSTART:'.$dtime.'
DTEND:'.$dtime1.'
DTSTAMP:20110525T075116Z
ORGANIZER;CN='.$result->display_name.':mailto:'.$result->user_email.'
UID:12345678
DESCRIPTION:'.$res->eve_desc.'
LOCATION: '.$res->event_venue.'
SEQUENCE:0
STATUS:CONFIRMED
SUMMARY:'.$res->event_title.'
TRANSP:OPAQUE
END:VEVENT
END:VCALENDAR';

				/*Setting the header part, this is important */
				$headers = "From: From Name <From Mail>\n";
				$headers .= "MIME-Version: 1.0\n";
				$headers .= "Content-Type: text/calendar; method=REQUEST;\n";
				$headers .= 'charset="UTF-8"';
				$headers .= "\n";
				$headers .= "Content-Transfer-Encoding: 7bit";

				/*mail content , attaching the ics detail in the mail as content*/
				$subject = "Meeting Subject";
				$subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');

				/*mail send*/
				if(wp_mail($user_email, $subject, $message, $headers)) {

					echo "sent";
				}else {
					echo "error";
				}

			}
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
                        $result2 = $wpdb->get_results("Select * from ".$eventtable." where event_id = '$eveid' AND event_status='0'");
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
                                    <td class="center"><a href="'.site_url().'/index.php/customer-area/pages/pending-invitations?status=yes&id='.$eveid.'&uid='.$cuid.'" class="links">Accepted</a>/<a href="'.site_url().'/index.php/customer-area/pages/pending-invitations?status=no&id='.$eveid.'&uid='.$cuid.'" class="links">Decline</a></td>
                              </tr>';}
                        }
                  }
                  echo 
                  '</tbody></table>';
            }
      }
}