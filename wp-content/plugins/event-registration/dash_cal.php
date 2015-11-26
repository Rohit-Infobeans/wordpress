<?php

function calendar_scripts1()
{
      // Register the scripts and style like this for a plugin:
      wp_register_style( 'fullcal', plugins_url( 'css/fullcalendar.min.css', __FILE__ ) );
      wp_register_script( 'fullcalendar_moment', plugins_url( 'js/moment.min.js', __FILE__ ), array( 'jquery' ) );
      wp_register_script( 'fullcalendar', plugins_url( 'js/fullcalendar.min.js', __FILE__ ), array( 'jquery' ) );     

      // For either a plugin or a theme, you can then enqueue the script:
      wp_enqueue_style( 'fullcal' );
      wp_enqueue_script('fullcalendar_moment');	
      wp_enqueue_script('fullcalendar');
      
}
add_action( 'wp_enqueue_scripts', 'calendar_scripts1' );

function dashboard_calendar1()
{
      global $wpdb, $table_prefix;
      //Table Names
      $eventtable =  $table_prefix . 'calendar';
      $usertable =  $table_prefix . 'event_users';
      
      //For creating jason_encode function if it does not exists
      if (!function_exists('json_encode'))
      {
            function json_encode($a=false)
            {
                  if (is_null($a)) return 'null';
                  if ($a === false) return 'false';
                  if ($a === true) return 'true';
                  if (is_scalar($a))
                  {
                        if (is_float($a))
                        {
                              // Always use "." for floats.
                              return floatval(str_replace(",", ".", strval($a)));
                        }

                        if (is_string($a))
                        {
                              static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
                              return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
                        }
                        else
                              return $a;
                  }
                  $isList = true;
                  for ($i = 0, reset($a); $i < count($a); $i++, next($a))
                  {
                        if (key($a) !== $i)
                        {
                              $isList = false;
                              break;
                        }
                  }
                  $result = array();
                  if ($isList)
                  {
                        foreach ($a as $v) $result[] = json_encode($v);
                        return '[' . join(',', $result) . ']';
                  }
                  else
                  {
                        foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
                        return '{' . join(',', $result) . '}';
                  }
            }
      }
      //For current logged in user information
      $current_user = wp_get_current_user();
      $cuid = $current_user->ID;
      
      $data = "";
      $event_user = $wpdb->get_results( "SELECT * FROM ".$usertable." where Eve_User_Id='$cuid' AND Accepted = '0' AND Declined = '1'"); 
      foreach($event_user as $row)
      {
            $eveid = $row->Eve_id;
            $events = $wpdb->get_row( "SELECT event_title, CONCAT(`event_begin`,'T',`event_stime`) as start, CONCAT(`event_end`,'T',`event_etime`) as end, event_repeats as id , event_recur FROM ".$eventtable." where event_id='$eveid' AND event_status='0'");
            
            $rep = $events->id;
            $recur = $events->event_recur;
            if($rep>0 && $recur !='S')
            {
                  $rows = array(); //This is used as an array for collecting event information in form of associated array
                  if($recur == 'W')
                  {
                        $rows[] = $events;
                        for($x=0;$x<$rep;$x++)
                        {
                              $rows = array();
                              $rows[] = $events;
                              $data .=  json_encode($rows);
                              $stime = explode('T',$events->start);
                              $etime = explode('T',$events->end);
                              $events->start = date("Y-m-d", strtotime(date("Y-m-d", strtotime($events->start)) . " + 7 day"))."T".$stime[1];
                              $events->end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($events->end)) . " + 7 day"))."T".$etime[1];
                        }
                  }
                  else if($recur == 'M')
                  {
                        $rows[] = $events;
                        for($x=0;$x<$rep;$x++)
                        {
                              $rows = array();
                              $rows[] = $events;
                              $data .=  json_encode($rows);
                              $stime = explode('T',$events->start);
                              $etime = explode('T',$events->end);
                              $events->start = date("Y-m-d", strtotime(date("Y-m-d", strtotime($events->start)) . " + 1 month "))."T".$stime[1];
                              $events->end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($events->end)) . " + 1 month "))."T".$etime[1];
                        }
                  }
                  else
                  {
                        $rows[] = $events;
                        for($x=0;$x<$rep;$x++)
                        {
                              $rows = array();
                              $rows[] = $events;
                              $data .=  json_encode($rows);
                              $stime = explode('T',$events->start);
                              $etime = explode('T',$events->end);
                              $events->start = date("Y-m-d", strtotime(date("Y-m-d", strtotime($events->start)) . " + 1 year"))."T".$stime[1];
                              $events->end = date("Y-m-d", strtotime(date("Y-m-d", strtotime($events->end)) . " + 1 year"))."T".$etime[1];
                        }
                  }
            }
            else
            {
                  $rows = array(); //This is used as an array for collecting event information in form of associated array
                  $rows[] = $events;
                  $data .=  json_encode($rows);
            }
            //For convertting array into JSON data
      }
      $data =  str_replace("][",",",$data);
      $data =  str_replace(",,"," ",$data);
      ?>
      <script>
            jQuery(document).ready(function() 
            {
                  jQuery('#calendar').fullCalendar(
                  {
                        <?php 
                              if(!empty($data))
                              {?>
                                    header: 
                                    {
                                          left: 'prev,next today',
                                          center: 'event_title',
                                          right: 'month,agendaWeek,agendaDay'
                                    },
                                    defaultDate: new Date(),
                                    editable: false,
                                    eventLimit: true, // allow "more" link when too many events
                                    events: <?php echo $data;?>
                              <?php }
                              else
                              {?>
                                    header: 
                                    {
                                          left: 'prev,next today',
                                          center: 'event_title',
                                          right: 'month,agendaWeek,agendaDay'
                                    },
                                    defaultDate: new Date(),
                                    editable: false,
                                    eventLimit: true
                             <?php }
                        ?>
                        
                  });
                  
            });
      </script> 
      <div id="calendar"></div>
<?php 
}