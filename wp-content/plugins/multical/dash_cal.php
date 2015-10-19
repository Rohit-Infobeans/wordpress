<?php
/*
      Plugin Name: Custom Calendar
      Plugin URI:
      Description: Updates user rating based on number of posts.
      Version: 1.0
      Author: 
      Author URI: 
*/
function calendar_scripts()
{
      // Register the library again from Google's CDN
      wp_register_script( 'jquery', 'http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js', array(), null, false );

      // Register the scripts and style like this for a plugin:
      
      wp_register_style( 'fullcal', plugins_url( 'css/fullcalendar.min.css', __FILE__ ) );
     
      wp_register_script( 'fullcalendar_moment', plugins_url( 'js/moment.min.js', __FILE__ ), array( 'jquery' ) );
      
      wp_register_script( 'fullcalendar', plugins_url( 'js/fullcalendar.min.js', __FILE__ ), array( 'jquery' ) );     
       

      // For either a plugin or a theme, you can then enqueue the script:
      
      
      wp_enqueue_style( 'fullcal' );
      //wp_enqueue_style( 'fullcalp' );
      wp_enqueue_script('fullcalendar_moment');	
      //wp_enqueue_script('fullcalendar_jquery');
      wp_enqueue_script('fullcalendar');
      
}
add_action( 'wp_enqueue_scripts', 'calendar_scripts' );


function dashboard_calendar()
{
            global $wpdb, $table_prefix;
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
      $result1 = $wpdb->get_results( "SELECT event_title as title, CONCAT(`event_begin`,'T',`event_stime`) as start, CONCAT(`event_end`,'T',`event_etime`) as end FROM ".$table_prefix."calendar");
      $rows = array();
      foreach($result1 as $row)
      {
            $rows[] = $row;
      }
//print json_encode($rows);
//die;
      ?>
<script>
      jQuery(document).ready(function() {
		
		jQuery('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			defaultDate: new Date(),
			editable: false,
			eventLimit: true, // allow "more" link when too many events
			events: <?php print json_encode($rows);?>

                  });
		
	});

</script>

      <div id="calendar"></div>
<?php }

//For dashboard calendar
add_shortcode( 'dashboard_calendar', 'dashboard_calendar_shortcode' );
function dashboard_calendar_shortcode() 
{
      ob_start();
      dashboard_calendar();
      return ob_get_clean();
}