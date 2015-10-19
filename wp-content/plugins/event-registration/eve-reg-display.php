<?php
function wptuts_scripts_load_cdn()
{
      // Register the library again from Google's CDN
      wp_register_script( 'jquery', 'http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js', array(), null, false );
wp_register_script( 'jquery2', 'http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js', array(), null, false );
      // Register the script like this for a plugin:
      wp_register_script( 'ui-script', plugins_url( 'js/jqueryui.js', __FILE__ ), array( 'jquery' ) );
      wp_register_script( 'custom-script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
      wp_register_script( 'validation', plugins_url( 'js/validation.js', __FILE__ ), array( 'jquery' ) );
      wp_register_style( 'eve-style', plugins_url( 'css/eve-reg.css', __FILE__ ) );
      // For either a plugin or a theme, you can then enqueue the script:
      wp_enqueue_script( 'ui-script' );
      wp_enqueue_script( 'custom-script' );
      wp_enqueue_script('validation');
      wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
      wp_enqueue_style( 'eve-style' );
      wp_enqueue_script('jquery2');
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
      
      //wp_enqueue_style( 'multipleuserstyle' );
      wp_enqueue_style( 'prism' );
      wp_enqueue_style( 'chosen' );
      
      wp_enqueue_script( 'chosenjQuery' );
      wp_enqueue_script( 'prismjs' );
      
      
}
add_action( 'wp_enqueue_scripts', 'wptuts_scripts_load_cdn' );


function registration_form($eve_title, $eve_sdate, $eve_tdate, $eve_stime, $repeat, $recur, $eve_venue, $eve_users, $desc) 
{
      global $wpdb, $table_prefix;
      $current_user = wp_get_current_user();
      $cid = $current_user->ID;
      $result1 = $wpdb->get_row( "SELECT event_id FROM ".$table_prefix."calendar ORDER BY event_id desc LIMIT 0,1");
      //echo $wpdb->last_query;
      //die;
      if(isset($result1 ->event_id) && empty($result1 ->event_id))
      {
            $i = 1;
      }
      else
      {
            $i = $result1->event_id;
      }
      echo '

      <form action="' . $_SERVER['REQUEST_URI'] . '" method="post" id="content1">
            <div class="input-text">
                  <input type="hidden" name = "event_id" id="eventid" value="'.($i+1).'" readonly />
                  <span id="generror"></span>
            </div>
            <div class="input-text">
                  <input type="text" name = "event_title" id="eventtitle" value="'.( isset( $_POST['event_title'] ) ? $eve_title : null ).'"placeholder="Event Title"/>
                  <span  id="error"></span>
            </div>
            <div class="input-text" id="basicExample">
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
                  
                            jQuery("#basicExample .time").timepicker({
                                  
                                "showDuration": true,
                                "timeFormat": "g:ia"
                            });

                            jQuery("#basicExample .date").datepicker({
                                  
                                "format": "yyyy/m/d",
                                "autoclose": true
                                
                            });

                            var basicExampleEl = document.getElementById("basicExample");
                            var datepair = new Datepair(basicExampleEl);
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
                  <input type="submit" name="submit" value="Create Event"/>
            </div>
      </form>
      ';

}

function edit_registration_form($eve_title, $eve_sdate, $eve_stime, $eve_tdate, $eve_ttime, $repeat, $recur, $eve_venue, $eve_users, $desc) 
{
      global $wpdb, $table_prefix;
      $id = $_GET['eid'];
      $i = 1;
      $result = $wpdb->get_results( "SELECT * FROM ".$table_prefix."calendar where event_id='$id'"); 
      foreach($result as $row)
      {
            echo '
            <script>
            jQuery( document ).ready(function() {
                   var dvRec = document.getElementById("recu");
    if('.$row->event_repeats.'!=0 )
                          {
                                
                              jQuery("#rec").prop("checked", true);
                              dvRec.style.display = "block";
                              jQuery("#rep").attr("placeholder", "Enter number of repetation");
                              jQuery("#recop").attr("placeholder", "Select when to repeat");
                              jQuery("#rep").attr("value", "'.$row->event_repeats.'");
                              jQuery("#recop").attr("value", "';
                                    if($row->event_recur=='S'){echo 'S';}
                                    else if($row->event_recur=='W'){echo 'W';}
                                    else if($row->event_recur=='M'){echo 'M';}
                                    else{echo 'Y';}
                                    
                              echo '");
                          }
                          else{
                                dvRec.style.display = "none";
                                    jQuery("#rep").attr("value", "0");
                                    jQuery("#recop").attr("value", "S");
                          }
            });
            </script>
            <form action="' . $_SERVER['REQUEST_URI'] . '" method="post" id="content1">
                  <div class="input-text">
                        <input type="hidden" name = "event_id" id="eventid" value="'.$row->event_id .'" readonly/>
                        
                  </div>
                  <div class="input-text">
                        <input type="text" name = "event_title" id="eventtitle" value="'.( isset( $_POST['event_title'] ) ? $eve_title : $row->event_title ).'"placeholder="Event Title"/>
                        <span  id="error"></span>
                  </div>
                  <div class="input-text" id="basicExample">
                    <input type="text" class="date start"  placeholder="Start Date" id="fdate" name = "start_date" value="'.( isset( $_POST['start_date'] ) ? $eve_sdate : $row->event_begin ).'"/>
                    <input type="text" class="date end" placeholder="End Date" id="tdate" name="to_date" value="'.( isset( $_POST['to_date'] ) ? $eve_tdate : $row->event_end ).'"/>
                     <input type="text" class="time start" placeholder="Start Time" id="ftime" name="start_time" value="'.( isset( $_POST['start_time'] ) ? $eve_stime : $row->event_stime ).'"/>
                    <input type="text" class="time end" placeholder = "End Time" id="ttime" name="to_time" value="'.( isset( $_POST['to_time'] ) ? $eve_ttime : $row->event_etime ).'"/>

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
                              jQuery("#ftime").attr("value", "'.date("h:ia", strtotime($row->event_stime)).'");
                              jQuery("#ttime").attr("value", "'.date("h:ia", strtotime($row->event_etime)).'");
                          }
                      }
                  
                            jQuery("#basicExample .time").timepicker({
                                  
                                "showDuration": true,
                                "timeFormat": "g:ia"
                            });

                            jQuery("#basicExample .date").datepicker({
                                "format": "yyyy/m/d",
                                "autoclose": true
                            });

                            var basicExampleEl = document.getElementById("basicExample");
                            var datepair = new Datepair(basicExampleEl);
                  </script>
                  <script type="text/javascript">
                              function RecDiv(rec) 
                            {
                                var dvRec = document.getElementById("recu");
                               
                                if(rec.checked)
                                {
                                    dvRec.style.display = "block";
                                    jQuery("#rep").attr("placeholder", "Enter number of repetation");
                                    jQuery("#recop").attr("placeholder", "Select when to repeat");
                                    jQuery("#rep").attr("value", "'.$row->event_repeats.'");
                                    jQuery("#recop").attr("value", "';
                                          if($row->event_recur=='S'){echo 'S';}
                                          else if($row->event_recur=='W'){echo 'W';}
                                          else if($row->event_recur=='M'){echo 'M';}
                                          else{echo 'Y';}
                                          
                                    echo '");
                                }
                                else
                                {
                                    dvRec.style.display = "none";
                                    jQuery("#rep").attr("value", "0");
                                    jQuery("#recop").attr("value", "S");
                                }
                            }
                      </script>
                  <input type="checkbox" id = "rec" name="rec" onclick="RecDiv(this)"> Recurrance<br/>
                  <span  id="error"></span>
            </div>
            
            <div class="input-text" id="recu">
                    
                  <input type="text" id="rep" name="event_repeats" class="input" size="1" value="'.( isset( $_POST['event_repeats'] ) ? $eve_venue : $row->event_repeats ).'">
                  <select name="event_recur" class="input" id="recop">
                              <option class="input" value="S">None</option>
                              <option class="input" value="W">Weeks</option>
                              <option class="input" value="M">Months (date)</option>
                              <option class="input" value="Y">Years</option>
                  </select>
                  <span  id="error"></span>
            </div>
                  <div class="input-text">
                        <input type="text" id="venue" name="venue" placeholder="Venue" value="'.( isset( $_POST['venue'] ) ? $eve_venue : $row->event_venue ).'"/><br />
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
                        <textarea placeholder="Event Description" rows="6" cols="100" name="desc">'.$row->event_desc.'</textarea>
                        <span  id="error"></span>
                  </div>
                  <div class="input-text">
                        <input type="submit" name="submit" value="Update"/>
                  </div>
            </form>';
      }
}

