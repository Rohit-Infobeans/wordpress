<?php
/*
  Plugin Name: New Calendar
  Plugin URI: http://code.tutsplus.com
  Description: Updates user rating based on number of posts.
  Version: 1.0
  Author: Agbonghama Collins
  Author URI: http://tech4sky.com
 */
 
 //$dir = plugin_dir_path( __FILE__ );
//require_once($dir.'backend_create.php');
//require_once($dir.'backend_events.php');
 
 function scripts_load_cdn()
{
	
	
    wp_register_script( 'cal_script1', plugins_url( 'js/daypilot.js', __FILE__ ), array( 'jquery' ) );
	wp_register_script( 'cal_script2', plugins_url( 'js/jquery-1.9.1.min.js', __FILE__ ), array( 'jquery' ) );
	
	
   
	
	
	

 
    wp_enqueue_script( 'cal_script1' );
	wp_enqueue_script( 'cal_script2' );
 
	
   
	
	
 
}
add_action( 'wp_enqueue_scripts', 'scripts_load_cdn' );

 function cal_function() {

	echo '
        
        <body>
       
        
        <div class="main">
            
            <div style="float:left; width: 160px;">
                <div></div>
            </div>
            <div style="margin-left: 160px;">
                <div></div>
            </div>

            <script type="text/javascript">
                
                var nav = new DayPilot.Navigator("nav");
                nav.showMonths = 3;
                nav.skipMonths = 3;
                nav.selectMode = "week";
                nav.onTimeRangeSelected = function(args) {
                    dp.startDate = args.day;
                    dp.update();
                    loadEvents();
                };
                nav.init();
                
                var dp = new DayPilot.Calendar("dp");
                dp.viewType = "Week";

                dp.onEventMoved = function (args) {
                    $.post("backend_move.php", 
                            {
                                id: args.e.id(),
                                newStart: args.newStart.toString(),
                                newEnd: args.newEnd.toString()
                            }, 
                            function() {
                                console.log("Moved.");
                            });
                };

                dp.onEventResized = function (args) {
                    $.post("backend_resize.php", 
                            {
                                id: args.e.id(),
                                newStart: args.newStart.toString(),
                                newEnd: args.newEnd.toString()
                            }, 
                            function() {
                                console.log("Resized.");
                            });
                };

                // event creating
                dp.onTimeRangeSelected = function (args) {
                    var name = prompt("New event name:", "Event");
                    dp.clearSelection();
                    if (!name) return;
                    var e = new DayPilot.Event({
                        start: args.start,
                        end: args.end,
                        id: DayPilot.guid(),
                        resource: args.resource,
                        text: name
                    });
                    dp.events.add(e);

                    $.post("backend_create.php", 
                            {
                                start: args.start.toString(),
                                end: args.end.toString(),
                                name: name
                            }, 
                            function() {
                                console.log("Created.");
                            });

                };

                dp.onEventClick = function(args) {
                    alert("clicked: " + args.e.id());
                };

                dp.init();

                loadEvents();

                function loadEvents() {
                    var start = dp.visibleStart();
                    var end = dp.visibleEnd();

                    $.post("backend_events.php", 
                    {
                        start: start.toString(),
                        end: end.toString()
                    }, 
                    function(data) {
                        //console.log(data);
                        dp.events.list = data;
                        dp.update();
                    });
                }

            </script>
            
            <script type="text/javascript">
            $(document).ready(function() {
                $("#theme").change(function(e) {
                    dp.theme = this.value;
                    dp.update();
                });
            });  
            </script>

        </div>
        <div class="clear">
        </div>
        
</body>';
	
	
    }  
add_shortcode( 'new_calendar', 'Calander_shortcode' ); 
// The callback function that will replace [book]
function Calander_shortcode() {
	ob_start();
	 cal_function();
   return ob_get_clean();
}