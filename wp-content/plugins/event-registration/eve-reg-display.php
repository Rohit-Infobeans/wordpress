<?php
add_action('wp_ajax_check_slot','check_slot');
add_action( 'wp_ajax_nopriv_check_slot', 'check_slot' );

function wptuts_scripts_load_cdn()
{
      // Register the library again from Google's CDN
      wp_register_script( 'jquery', 'http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js', array(), null, false );
      wp_register_script( 'jquery2', 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js', array(), null, false );
      wp_register_script( 'jquery3', 'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js', array(), null, false );
      // Register the script like this for a plugin:
      wp_register_script( 'ui-script', plugins_url( 'js/jqueryui.js', __FILE__ ), array( 'jquery' ) );
      wp_register_script( 'custom-script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
      wp_register_script( 'validation', plugins_url( 'js/validation.js', __FILE__ ), array( 'jquery' ) );
      wp_register_style( 'eve-style', plugins_url( 'css/eve-reg.css', __FILE__ ) );

      wp_enqueue_script( 'ui-script' );
      wp_enqueue_script( 'custom-script' );
      wp_enqueue_script('validation');
      wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
      wp_enqueue_style( 'eve-style' );
      wp_enqueue_script('jquery2');
      wp_enqueue_script('jquery');
      
      //For date and time picker
      wp_register_script( 'jQuerytimepicker', plugins_url( 'js/jquery.timepicker.js', __FILE__ ), array( 'jquery' ) );
      wp_register_script( 'bootstrapdatepicker', plugins_url( 'js/bootstrap-datepicker.js', __FILE__ ), array( 'jquery' ) );
      wp_register_script( 'datepair', plugins_url( 'js/datepair.js', __FILE__ ), array( 'jquery' ) );
      wp_register_script( 'jQuerydatepair', plugins_url( 'js/jquery.datepair.js', __FILE__ ), array( 'jquery' ) );
      wp_register_style( 'bootstrstrapcss', plugins_url( 'css/bootstrap-datepicker.css', __FILE__ ) );
      wp_register_style( 'jQuerytimepickercss', plugins_url( 'css/jquery.timepicker.css', __FILE__ ) );
      
      wp_enqueue_script( 'jQuerytimepicker' );
      wp_enqueue_script( 'bootstrapdatepicker' );
      wp_enqueue_script( 'datepair' );
      wp_enqueue_script( 'jQuerydatepair' );
      wp_enqueue_style( 'bootstrstrapcss' );
      wp_enqueue_style( 'jQuerytimepickercss' );

      //For multiple user
      wp_register_style( 'multipleuserstyle', plugins_url( 'css/multipleuser.css', __FILE__ ) );
      wp_register_style( 'prism', plugins_url( 'css/prism.css', __FILE__ ) );
      wp_register_style( 'chosen', plugins_url( 'css/chosen.css', __FILE__ ) );
      wp_register_script( 'chosenjQuery', plugins_url( 'js/chosen.jquery.js', __FILE__ ), array( 'jquery' ) );
      wp_register_script( 'prismjs', plugins_url( 'js/prism.js', __FILE__ ), array( 'jquery' ) );

      wp_enqueue_style( 'prism' );
      wp_enqueue_style( 'chosen' );    
      wp_enqueue_script( 'chosenjQuery' );
      wp_enqueue_script( 'prismjs' );
      
      //For pagination
      wp_register_style( 'table_bootstrap', plugins_url( 'css/Tables/bootstrap.min.css', __FILE__ ) );
      wp_register_style( 'datatable_bootstrap', plugins_url( 'css/Tables/dataTables.bootstrap.css', __FILE__ ) );

      wp_enqueue_style( 'table_bootstrap');
      wp_enqueue_style( 'datatable_bootstrap');    

}
add_action( 'wp_enqueue_scripts', 'wptuts_scripts_load_cdn' );



//Function resplonsible for calling functions (for displaying event registration form, serverside validation and inserting in database)
function custom_registration_function() 
{
      global $wpdb, $eventtable , $usertable;
      
      //To get information about current logged in user
      $current_user = wp_get_current_user();
      $cuser = $current_user->user_login;     
      $cuid = $current_user->ID;
      
      if ( isset($_POST['submit'] ) ) 
      {
            global $title, $sdate, $stime, $tdate, $repeat, $recur, $venue, $users, $desc, $status;
            $title   =   $_POST['event_title'];
            $sdate   =   $_POST['start_date'] ;
            $tdate    = $_POST['to_date'] ;
            $stime      = $_POST['start_time'];
            $ttime    = $_POST['to_time'] ;
            $repeat = $_POST['event_repeats'];
            $recur = $_POST['event_recur'];
            $venue  =   $_POST['venue'] ;
            $users = $_POST['traditional'] ;
            $desc = $_POST['desc'] ;
            $status = 0;
            //Start and end time converted in 24 hour formate
            $estime = date("H:i", strtotime($stime));
            $ettime = date("H:i", strtotime($ttime));
            
            //For calling registration_validation function
            registration_validation($title, $sdate, $stime, $tdate, $ttime, $repeat, $recur, $venue, $users, $desc);
            //After completing validation complete_registration is used to call another function to insert data in database if there is no error
            complete_registration( $title, $sdate, $tdate, $stime, $ttime, $repeat, $recur, $venue, $users, $desc, $status); 
						
      }
      
      registration_form( $eve_title, $eve_sdate, $eve_tdate, $eve_stime, $eve_ttime, $repeat, $recur, $eve_venue, $eve_users, $desc);
}
	
	



//Function to insert data of new event in database table
function complete_registration($title, $sdate, $tdate,$stime, $ttime, $repeat, $recur, $venue, $users, $desc, $status)
{
      global $wpdb, $table_prefix, $reg_errors, $eventtable , $usertable;
      
      //To get information about current logged in user
      $current_user = wp_get_current_user();
      $cuser = $current_user->user_login;     
      $cuid = $current_user->ID;
      
      $event_status= $accepted = $decline = 0;
      
      if ( 1 > count( $reg_errors->get_error_messages() ) ) 
      {
            //For users
            $user = $_POST['traditional'];
            $user[count($user)] = $cuid;
            $u = implode(",", $user);
            
            //Start and end time converted in 24 hour formate
            $estime = date("H:i", strtotime($stime));
            $ettime = date("H:i", strtotime($ttime));
            
            //For inserting data in wp_calendar
            //$event_info is for event information
            //$event_formats is for the field formats
            $event_info = array( 
                  'event_begin' => $sdate,
                  'event_end' => $tdate,
                  'event_title' => $title,
                  'event_desc' => $desc,
                  'event_venue' => $venue,
                  'event_stime' => $estime,
                  'event_etime'=>$ettime,
                  'event_recur'=> $recur,
                  'event_repeats'=> $repeat,
                  'event_author' => $cuid,
                  'event_category'=>null,
                  'event_link'=>null,
                  'event_datecreated' => date('Y-m-d H:i:s'),
                  'event_status' => $event_status
            );

            $event_formats = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d'); 
            $wpdb->insert($eventtable, $event_info, $event_formats);
            
            //For inserting data in wp_event_users
            $event_user =explode(",", $u);
            for($i=0;$i<count($event_user);$i++)
            {
                  $eid = $_POST['event_id'];
                  //$user_event is for the users info invited for the event
                  $user_event = array( 
                        'Eve_id' => $eid,
                        'Eve_User_Id' => $event_user[$i], 
                        'Accepted' => $accepted,
                        'Declined' => $decline, 
                        'Eve_Status' => $status
                  );
                  //Format for user table fields
                  $user_formats = array( '%s', '%s', '%d', '%d', '%d');
                  $wpdb->insert($usertable, $user_event, $user_formats);
                  
                  //To mail each and every user selected by author
                  $result = $wpdb->get_row( "SELECT user_email FROM ".$table_prefix."users WHERE id='$event_user[$i]'");
                  $bnfw = BNFW::factory();
                  if ($bnfw->notifier->notification_exists('new-event'))
                  {
                        $notifications = $bnfw->notifier->get_notifications('new-event');
                        foreach ($notifications as $notification) 
                        {
                              $setting = $bnfw->notifier->read_settings($notification->ID);
                              $to = $result->user_email;
                              $message  = '		
                              <div id="email_container" style="background:#444">
                                    <div style="width:570px; padding:0 0 0 20px; margin:50px auto 12px auto" id="email_header">
                                          <span style="background:#585858; color:#fff; padding:12px;font-family:trebuchet ms; letter-spacing:1px; 
                                                -moz-border-radius-topleft:5px; -webkit-border-top-left-radius:5px; 
                                                border-top-left-radius:5px;moz-border-radius-topright:5px; -webkit-border-top-right-radius:5px; 
                                                border-top-right-radius:5px;">
                                                Event Invitation</span>
                                          </div>
                                    </div>
                                    <div style="width:550px; padding:0 20px 20px 20px; background:#fff; margin:0 auto; border:3px #000 solid;
                                          moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; color:#454545;line-height:1.5em; " id="email_content">
                                          <h1 style="padding:5px 0 0 0; font-family:georgia;font-weight:500;font-size:24px;color:#000;border-bottom:1px solid #bbb">
                                                This is the invitaion for '.$title.'
                                          </h1>
                                          <p>
                                                This event is organized by '.$cuser.'. Related informations <br/>Venue: '
                                                .$venue.'<br/>Event Starts at: '.$sdate.' '.$stime.'<br/>Event ends at: '.$tdate.' '.$ttime.'<br/>Event description: '.$desc.'
                                          </p>
                                          <p>
                                                <a href="'.site_url().'/index.php/customer-area/pages/pending-invitations?status=yes&id='.$eid.'&uid='.$event_user[$i].'"><span class="myButton" id="myButton" style="box-shadow: rgb(207, 134, 108) 0px 1px 0px 0px inset; border-radius: 3px; border: 1px solid rgb(148, 41, 17); display: inline-block; cursor: pointer; color: rgb(255, 255, 255); font-family: Arial; font-size: 13px; padding: 6px 24px; text-decoration: none; text-shadow: rgb(133, 70, 41) 0px 1px 0px; background: linear-gradient(rgb(208, 69, 27) 5%, rgb(188, 51, 21) 100%) rgb(208, 69, 27);">Accept</span></a>
                                                <a href="'.site_url().'/index.php/customer-area/pages/pending-invitations?status=no&id='.$eid.'&uid='.$event_user[$i].'"><span class="myButton" id="myButton" style="box-shadow: rgb(207, 134, 108) 0px 1px 0px 0px inset; border-radius: 3px; border: 1px solid rgb(148, 41, 17); display: inline-block; cursor: pointer; color: rgb(255, 255, 255); font-family: Arial; font-size: 13px; padding: 6px 24px; text-decoration: none; text-shadow: rgb(133, 70, 41) 0px 1px 0px; background: linear-gradient(rgb(208, 69, 27) 5%, rgb(188, 51, 21) 100%) rgb(208, 69, 27);">Decline</span></a>
                                          </p> 				
                                    </div>
                              </div>';
                              $subject = $setting['subject'];
                              $headers= "MIME-Version: 1.0\n" ."Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n";
							  $attachments = array( WP_CONTENT_DIR . '/plugins/event-registration/google-api/.credentials/invite.ics' );
                              wp_mail( $to,  $subject , wpautop( $message  ), $headers);
                        } 
                  }
            }
            //To redirect to another page
			
            wp_redirect(site_url().'/index.php/customer-area/events-lists/created-by-me');
            exit;
      }
}



function check_slot() 
{
      global $wpdb ,$usertable, $eventtable;
      $sdate = $_REQUEST['s_date'];
      $stime = $_REQUEST['s_time'];
      $cid = $_REQUEST['c_id'];
      $eve_num=0;
      $estime = date("H:i:s", strtotime($stime));
      $result = $wpdb->get_results("Select * from ".$usertable." where Eve_User_Id='$cid' AND Accepted='0' AND Declined = '1'");
      
      foreach($result as $row)
      {
            $eve_id = $row->Eve_id;
            $resul = $wpdb->get_row("Select * from ".$eventtable." where event_id='$eve_id' AND event_status='0'");
            
            if($sdate == $resul->event_begin && $estime == $resul->event_stime)
            {
                  $eve_num = $eve_num +1;
            }
      }
      if($eve_num !=0)
      {
            echo 1;
      }
      else
      {
            echo "Not matched";
      }
}

function registration_form($eve_title, $eve_sdate, $eve_tdate, $eve_stime, $repeat, $recur, $eve_venue, $eve_users, $desc) 
{
      global $wpdb, $usertable, $eventtable;
      
      $current_user = wp_get_current_user();
      $cid = $current_user->ID;
      
      $result1 = $wpdb->get_row( "SELECT event_id FROM ".$eventtable." ORDER BY event_id desc LIMIT 0,1");
      if(isset($result1 ->event_id) && empty($result1 ->event_id))
      {
            $i = 1;
      }
      else
      {
            $i = $result1->event_id;
      }
      ?>
      <script>
      jQuery(document).ready(function(){
            jQuery('#ftime').change(check_slot);
      })
      function check_slot()
      {	
            var sdate = jQuery('#fdate').val();
            var stime = jQuery('#ftime').val();
            var cid = <?php echo $cid;?>;
            jQuery.ajax({
                  type: "POST",
                  url : '<?php echo admin_url('admin-ajax.php'); ?>',
                  data : {action: "check_slot", s_date : sdate, s_time: stime, c_id: cid},
                  success:function(response) 
                  {
                        
                        if(response == 10)
                        {
                              jQuery('#venue').prop( "readonly", true );
                              var con = confirm("Time slot is booked for another event. Do you want to continue?");
                              if(con == true)
                              {
                                    jQuery('#venue').prop( "readonly", false);
                              }
                              else
                              {
                                    location.href="<?php echo site_url().'/index.php/customer-area/events-lists/created-by-me/';?>";
                              }
                        }
                        
                  }
            }); 
      }
      </script>
      <?php
      echo '
      <form action="' . $_SERVER['REQUEST_URI'] . '" method="post" id="content1">
            <div class="input-text">
                  <input type="hidden" name = "event_id" id="eventid" value="'.($i+1).'" readonly />   
            </div>
            <div class="input-text">
                  <input type="text" name = "event_title" id="eventtitle" value="'.( isset( $_POST['event_title'] ) ? $eve_title : null ).'"placeholder="Event Title" />
                  <span  id="error"></span>
            </div>
            <div class="input-text" id="dateandtime">
                  <input type="text" class="date start"  placeholder="Start Date" id="fdate" name = "start_date"/>
                  <input type="text" class="date end" placeholder="End Date" id="tdate" name="to_date"/>
                  <input type="text" class="time start" placeholder="Start Time" id="ftime" name="start_time"/>
                  <input type="text" class="time end" placeholder = "End Time" id="ttime" name="to_time"/>
                   
                  <input type="checkbox" id = "ad" name="allday" value="All Day" onclick="ShowHideDiv(this)"> All Day<br/>
                  <script type="text/javascript">
                        function ShowHideDiv(ad) 
                        {
                              var sTime = document.getElementById("ftime");
                              var tTime = document.getElementById("ttime");
                              if(ad.checked)
                              {
                                    sTime.style.display = "none";
                                    tTime.style.display = "none";
                                    jQuery("#ftime").attr("value", "10:00am");
                                    jQuery("#ttime").attr("value", "07:00pm");
                              }
                              else
                              {
                                    sTime.style.display = "inline-block";
                                    tTime.style.display = "block";
                                    jQuery("#ftime").attr("value", "");
                                    jQuery("#ttime").attr("value", "");
                              }
                        }

                        jQuery("#dateandtime .time").timepicker({
                              "showDuration": true,
                              "timeFormat": "g:ia"
                        });
                        
                        jQuery("#dateandtime .date").datepicker({
                              "format": "yyyy-mm-dd",
                              "autoclose": true
                        });
                        
                        var dateandtimeEl = document.getElementById("dateandtime");
                        var datepair = new Datepair(dateandtimeEl);
                  </script>
                  <input type="checkbox" id = "rec" name="allday" onclick="RecDiv(this)"> Recurrance<br/>
                  <span  id="error"></span>
            </div>
            <div class="input-text" id="recu">
                  <script type="text/javascript">
                        function RecDiv(rec) 
                        {
                              var dvRec = document.getElementById("recu");
                              if(rec.checked)
                              {
                                    dvRec.style.display = "block";
                                    jQuery("#rep").attr("placeholder", "Enter number of repetation");
                                    jQuery("#recop").attr("placeholder", "Select when to repeat");
                                    jQuery("#rep").attr("value", "");
                                    jQuery("#recop").attr("value", "S");
                              }
                              else
                              {
                                    dvRec.style.display = "none";
                                    jQuery("#rep").attr("value", "0");
                                    jQuery("#recop").attr("value", "S");
                              }
                        }
                  </script>
                  <input type="text" id="rep" name="event_repeats" class="input" size="1" value="0">
                  <select name="event_recur" class="input" id="recop">
                        <option class="input" value="S">None</option>
                        <option class="input" value="W">Weeks</option>
                        <option class="input" value="M">Months (date)</option>
                        <option class="input" value="Y">Years</option>
                  </select>
                  <span  id="error"></span>
            </div>
            <div class="input-text">
                  <input type="text" id="venue" name="venue" placeholder="Venue" value="'.( isset( $_POST['venue'] ) ? $eve_venue : null ).'"/><br />
                  <span  id="error"></span>
            </div>
            <div class="input-text">
                  <select data-placeholder="Add Guests" class="chosen-select" multiple name="traditional[]">';
                        /* wpdb class should not be called directly.global $wpdb variable is an instantiation of the class already set up to talk to the WordPress database */ 
                        $result = $wpdb->get_results( "SELECT * FROM wp_users where ID != '$cid'  "); /*mulitple row results can be pulled from the database with get_results function and outputs an object which is stored in $result */
                        foreach($result as $row)
                        {
                              echo '<option value="'.$row->ID.'">'.$row->display_name.'</option>';   
                        }
                        echo '
                  </select>
                  <script type="text/javascript">
                        var config = {
                              ".chosen-select"           : {},
                              ".chosen-select-deselect"  : {allow_single_deselect:true},
                              ".chosen-select-no-single" : {disable_search_threshold:10},
                              ".chosen-select-no-results": {no_results_text:"Oops, nothing found!"},
                              ".chosen-select-width"     : {width:"95%"}
                        }
                        for (var selector in config) {
                              jQuery(selector).chosen(config[selector]);
                        }
                  </script>
                  <span  id="error"></span>
            </div>
            <div class="input-text">
                  <textarea placeholder="Event Description" rows="6" cols="100" name="desc" id="evedesc"></textarea>
                  <span  id="error"></span>
            </div>
            <div class="input-text">
                  <button type="submit" name="submit" id="sub">Submit</button>
            </div>
      </form>';
}
	

function edit_event()
{
      if ( isset($_POST['submit'] ) ) 
      {
            global $title, $sdate, $stime, $tdate, $repeat, $recur, $venue, $users, $desc, $status;
            $title   =   $_POST['event_title'] ;
            $sdate   =   $_POST['start_date'] ;
            $stime      = $_POST['start_time'];
            $tdate    = $_POST['to_date'] ;
            $ttime    = $_POST['to_time'] ;
            $repeat = $_POST['event_repeats'];
            $recur = $_POST['event_recur'];
            $venue  =   $_POST['venue'] ;
            $users   =   $_POST['traditional'] ;
            $users = implode(",", (array)$user);
            $desc        =   $_POST['desc'] ;
            $status = 0;
            
            registration_validation( $title, $sdate, $stime, $tdate, $ttime, $repeat, $recur, $venue, $users, $desc);
            
            complete_editeve($title, $sdate, $stime, $tdate, $ttime, $repeat, $recur, $venue, $users, $desc, $status);
      }
      edit_registration_form($eve_title, $eve_sdate, $eve_stime, $eve_tdate, $eve_ttime, $repeat, $recur, $eve_venue, $eve_users, $desc);
}



//Function to updating event information
function  complete_editeve($title, $sdate, $stime, $tdate, $ttime, $repeat, $recur, $venue, $users, $desc, $status) 
{
      global $wpdb, $table_prefix, $eventtable , $usertable, $reg_errors;
      $status = 0;
      $active = null;
      $decline = null;

      //To get information about logged in user
      $current_user = wp_get_current_user();
      $cuser = $current_user->user_login;
      $cuid = $current_user->ID;

      //Start and end time converted in 24 hour formate
      $estime = date("H:i", strtotime($stime));
      $ettime = date("H:i", strtotime($ttime));

      if ( 1 > count( $reg_errors->get_error_messages() ) ) 
      {
            //For inserting data in wp_calendar
            //$event_uinfo is for the updated information of event
            $event_uinfo = array( 
                        'event_begin' => $sdate,
                        'event_end' => $tdate,
                        'event_title' => $title, 
                        'event_desc' => $desc,
                        'event_venue' => $venue,
                        'event_stime' => $estime,
                        'event_etime' => $ettime,
                        'event_recur'=> $recur,
                        'event_repeats'=> $repeat,
                        'event_category'=>null,
                        'event_link'=>null,
            );
            //Formate
            $event_uformat = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'); 
            //Where condition for update query
            $whe=array('event_id'=> $_POST['event_id']);
            $wpdb->update($eventtable, $event_uinfo, $whe, $event_uformat);
            
            //For updating wp_event_users table
            $event_users =explode(",", $u);
            $eventid = $_POST['event_id'];
            if($users != null)
            {
                  for($i=0;$i<count($event_users);$i++)
                  {
                        $eve_users = $wpdb->get_results("SELECT * FROM".$usertable." WHERE Eve_id ='$eventid' AND Eve_User_Id='$event_users[$i]'");
                        if(count($eve_users) == 0)
                        {
                              //$nuser is for the new user invited by the author
                              $nuser = array( 
                                    'Eve_id' => $eventid,
                                    'Eve_User_Id' => $eu[$i], 
                                    'Accepted' => $active,
                                    'Declined' => $decline, 
                                    'Eve_Status' => $status
                              );
                              $nuser_format = array(  '%s', '%s', '%d', '%d', '%d'); 
                              $wpdb->insert($usertable, $nuser, $nuser_format);
                        }
                        else
                        {
                              exit;
                        }
                        
                  }
            }
            $event_user = $wpdb->get_results( "SELECT * FROM ".$usertable." WHERE Eve_id ='$eventid'");
            foreach($event_user as $row)
            {
                  $userid = $row->Eve_User_Id;
                  $result1 = $wpdb->get_row( "SELECT user_email FROM ".$table_prefix."users WHERE ID ='$userid'");
                  $bnfw = BNFW::factory();
                  if ($bnfw->notifier->notification_exists('edit-event'))
                  {
                        $notifications = $bnfw->notifier->get_notifications('edit-event');
                        foreach ($notifications as $notification) 
                        {
                              $setting = $bnfw->notifier->read_settings($notification->ID);
                              $to = $result->user_email;
                              $message  = '
                              <div id="email_container" style="background:#444">
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
                                                 '.$title.' Information updated
                                          </h1>
                                          
                                          <p>
                                                This event is organized by '.$cuser.'. Updation in informations <br/>Venue: '
                                                .$venue.'<br/>Event Starts at: '.$sdate.' '.$stime.'<br/>Event ends at: '.$tdate.' '.$ttime.'<br/>Event description: '.$desc.'
                                          </p>
                                          <p>
                                                <a href="'.site_url().'/index.php/customer-area/pages/pending-invitations?status=yes&id='.$eventid.'&uid='.$cuid.'"><span class="myButton" id="myButton" style="box-shadow: rgb(207, 134, 108) 0px 1px 0px 0px inset; border-radius: 3px; border: 1px solid rgb(148, 41, 17); display: inline-block; cursor: pointer; color: rgb(255, 255, 255); font-family: Arial; font-size: 13px; padding: 6px 24px; text-decoration: none; text-shadow: rgb(133, 70, 41) 0px 1px 0px; background: linear-gradient(rgb(208, 69, 27) 5%, rgb(188, 51, 21) 100%) rgb(208, 69, 27);">Accept</span></a>
                                                 
                                                <a href="'.site_url().'/index.php/customer-area/pages/pending-invitations?status=no&id='.$eid.'&uid='.$cuid.'"><span class="myButton" id="myButton" style="box-shadow: rgb(207, 134, 108) 0px 1px 0px 0px inset; border-radius: 3px; border: 1px solid rgb(148, 41, 17); display: inline-block; cursor: pointer; color: rgb(255, 255, 255); font-family: Arial; font-size: 13px; padding: 6px 24px; text-decoration: none; text-shadow: rgb(133, 70, 41) 0px 1px 0px; background: linear-gradient(rgb(208, 69, 27) 5%, rgb(188, 51, 21) 100%) rgb(208, 69, 27);">Decline</span></a>
                                          </p> 				
                                    </div>
                              </div>';
                              $subject = $setting['subject'];
							  $headers= "MIME-Version: 1.0\n" ."Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\n"; 
							  wp_mail( $to,  $subject , wpautop( $message  )  ,$headers);
                        } 
                  }
            }
            wp_redirect(site_url().'/index.php/customer-area/events-lists/created-by-me/');
            exit;
      }
}

function edit_registration_form($eve_title, $eve_sdate, $eve_stime, $eve_tdate, $eve_ttime, $repeat, $recur, $eve_venue, $eve_users, $desc) 
{
      global $wpdb, $usertable, $eventtable;
      $id = $_GET['eid'];
      $i = 1;
      $result = $wpdb->get_row( "SELECT * FROM ".$eventtable." where event_id='$id'"); 
      echo '
      <script>
            jQuery( document ).ready(function() {
                  var dvRec = document.getElementById("recu");
                  if('.$result->event_repeats.'!=0 )
                  {
                        jQuery("#rec").prop("checked", true);
                        dvRec.style.display = "block";
                        jQuery("#rep").attr("placeholder", "Enter number of repetation");
                        jQuery("#recop").attr("placeholder", "Select when to repeat");
                        jQuery("#rep").attr("value", "'.$result->event_repeats.'");
                        jQuery("#recop").attr("value", "';
                              if($result->event_recur=='S'){echo 'S';}
                              else if($result->event_recur=='W'){echo 'W';}
                              else if($result->event_recur=='M'){echo 'M';}
                              else{echo 'Y';}
                        echo '");
                  }
                  else
                  {
                        dvRec.style.display = "none";
                        jQuery("#rep").attr("value", "0");
                        jQuery("#recop").attr("value", "S");
                  }
            });
      </script>
      <form action="' . $_SERVER['REQUEST_URI'] . '" method="post" id="content1">
            <div class="input-text">
                  <input type="hidden" name = "event_id" id="eventid" value="'.$result->event_id .'" readonly/>
            </div>
            <div class="input-text">
                  <input type="text" name = "event_title" id="eventtitle" value="'.( isset( $_POST['event_title'] ) ? $eve_title :$result->event_title ).'"placeholder="Event Title"/>
                  <span  id="error"></span>
            </div>
            <div class="input-text" id="dateandtime">
                  <input type="text" class="date start"  placeholder="Start Date" id="fdate" name = "start_date" value="'.( isset( $_POST['start_date'] ) ? $eve_sdate : $result->event_begin ).'"/>
                  <input type="text" class="date end" placeholder="End Date" id="tdate" name="to_date" value="'.( isset( $_POST['to_date'] ) ? $eve_tdate : $result->event_end ).'"/>
                  <input type="text" class="time start" placeholder="Start Time" id="ftime" name="start_time" value="'.( isset( $_POST['start_time'] ) ? $eve_stime : $result->event_stime ).'"/>
                  <input type="text" class="time end" placeholder = "End Time" id="ttime" name="to_time" value="'.( isset( $_POST['to_time'] ) ? $eve_ttime : $result->event_etime ).'"/>

                  <input type="checkbox" id = "ad" name="allday" value="All Day" onclick="ShowHideDiv(this)"> All Day<br/>
                  <script type="text/javascript">
                        function ShowHideDiv(ad) 
                        {
                              var sTime = document.getElementById("ftime");
                              var tTime = document.getElementById("ttime");
                              if(ad.checked)
                              {
                                    sTime.style.display = "none";
                                    tTime.style.display = "none";
                                    jQuery("#ftime").attr("value", "10:00am");
                                    jQuery("#ttime").attr("value", "07:00pm");
                              }
                              else
                              {
                                    sTime.style.display = "inline-block";
                                    tTime.style.display = "block";
                                    jQuery("#ftime").attr("value", "'.date("h:ia", strtotime($result->event_stime)).'");
                                    jQuery("#ttime").attr("value", "'.date("h:ia", strtotime($result->event_etime)).'");
                              }
                        }
                        jQuery("#dateandtime.time").timepicker({
                              "showDuration": true,
                              "timeFormat": "g:ia"
                        });
                        
                        jQuery("#dateandtime.date").datepicker({
                              "format": "yyyy/m/d",
                              "autoclose": true
                        });

                        var dateandtimeEl = document.getElementById("dateandtime");
                        var datepair = new Datepair(dateandtimeEl);
                  </script>
                  <input type="checkbox" id = "rec" name="rec" onclick="RecDiv(this)"> Recurrance<br/>
                  <span  id="error"></span>
            </div>
            <div class="input-text" id="recu">
                  <input type="text" id="rep" name="event_repeats" class="input" size="1" value="'.( isset( $_POST['event_repeats'] ) ? $eve_venue : $result->event_repeats ).'">
                  <select name="event_recur" class="input" id="recop">
                        <option class="input" value="S">None</option>
                        <option class="input" value="W">Weeks</option>
                        <option class="input" value="M">Months (date)</option>
                        <option class="input" value="Y">Years</option>
                  </select>
                  <span  id="error"></span>
            </div>
            <div class="input-text">
                  <input type="text" id="venue" name="venue" placeholder="Venue" value="'.( isset( $_POST['venue'] ) ? $eve_venue : $result->event_venue ).'"/><br />
                  <span  id="error"></span>
            </div>
            <div class="input-text">';
                  $result1 = $wpdb->get_results( "SELECT * FROM wp_event_users where Eve_id='$id'");
                  foreach($result1 as $row1)
                  {
                        $euser = $row1->Eve_User_Id;
                        //echo "SELECT * FROM wp_users where ID='$euser'";
                        $result2 = $wpdb->get_row( "SELECT * FROM wp_users where ID='$euser'");
                        echo $result2->user_login.'<br/>';
                  } 
                  echo '
                  <select data-placeholder="Add Guests" class="chosen-select" multiple name="traditional[]">';
                        $result3 = $wpdb->get_results( "SELECT * FROM ".$table_prefix."users WHERE ID NOT IN (SELECT Eve_User_Id FROM ".$table_prefix."event_users WHERE Eve_id='$id')");
                        foreach($result3 as $row3)
                        {
                              echo '<option value="'.$row3->ID.'">'.$row3->display_name.'</option>';
                        }
                        echo '
                  </select>
                  <script type="text/javascript">
                        var config = {
                              ".chosen-select"           : {},
                              ".chosen-select-deselect"  : {allow_single_deselect:true},
                              ".chosen-select-no-single" : {disable_search_threshold:10},
                              ".chosen-select-no-results": {no_results_text:"Oops, nothing found!"},
                              ".chosen-select-width"     : {width:"95%"}
                        }
                        for (var selector in config) {
                              jQuery(selector).chosen(config[selector]);
                        }
                  </script>
                  <span  id="error"></span>
            </div>
            <div class="input-text">
                  <textarea placeholder="Event Description" rows="6" cols="100" name="desc">'.$result->event_desc.'</textarea>
                  <span  id="error"></span>
            </div>
            <div class="input-text">
                  <input type="submit" name="submit" value="Update"/>
            </div>
      </form>';
}


//Function for server side validations
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