<?php
$dir = plugin_dir_path( __FILE__ );
require_once($dir.'eve-validation.php');


function wptuts_scripts_load_cdn()
{
    // Register the library again from Google's CDN
    wp_register_script( 'jquery', 'http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js', array(), null, false );
    
    // Register the script like this for a plugin:
    wp_register_script( 'ui-script', plugins_url( 'js/jqueryui.js', __FILE__ ), array( 'jquery' ) );
    wp_register_script( 'custom-script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
    wp_register_script( 'multiple_user1', plugins_url( 'js/multiple_jquery.js', __FILE__ ), array( 'jquery' ) );
    wp_register_script( 'multiple_user2', plugins_url( 'js/selectivity-full.js', __FILE__ ), array( 'jquery' ) );
    wp_register_script( 'multiple_user3', plugins_url( 'js/multi.js', __FILE__ ), array( 'jquery' ) );

    wp_register_script( 'datetimejs1', plugins_url( 'js/bootstrap-datepicker.js', __FILE__ ), array( 'jquery' ) );
    wp_register_script( 'datetimejs2', plugins_url( 'js/datepair.js', __FILE__ ), array( 'jquery' ) );
    wp_register_script( 'datetimejs3', plugins_url( 'js/jquery.datepair.js', __FILE__ ), array( 'jquery' ) );
    wp_register_script( 'datetimejs4', plugins_url( 'js/jquery.timepicker.js', __FILE__ ), array( 'jquery' ) );
    
    wp_register_style( 'eve-style', plugins_url( 'css/eve-reg.css', __FILE__ ) );
    wp_register_style( 'multi', plugins_url( 'css/selectivity-full.min.css', __FILE__ ) );
    wp_register_style( 'datetime1', plugins_url( 'css/jquery.timepicker.css', __FILE__ ) );
    wp_register_style( 'datetime2', plugins_url( 'css/bootstrap-datepicker.css', __FILE__ ) );
   
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'ui-script' );
    wp_enqueue_script( 'custom-script' );
    wp_enqueue_script('multiple_user1');
    wp_enqueue_script('multiple_user2');
    wp_enqueue_script('multiple_user3');   

    wp_enqueue_script('datetimejs1');
    wp_enqueue_script('datetimejs2');
    wp_enqueue_script('datetimejs3');
    wp_enqueue_script('datetimejs4');
    wp_enqueue_script('jquery-ui-datepicker');	
    wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    wp_enqueue_style( 'eve-style' );
    wp_enqueue_style('multi');
    wp_enqueue_style( 'datetime1' );
    wp_enqueue_style( 'datetime2' );
}

add_action( 'wp_enqueue_scripts', 'wptuts_scripts_load_cdn' );


 function registration_form( $eve_title, $eve_sdate, $eve_stime, $eve_tdate, $eve_ttime, $eve_venue, $eve_users) 
 {
    echo '
    <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <div class="input-text">
                  <input type="text" name = "event_title" id="eventtitle" value="'.( isset( $_POST['event_title'] ) ? $eve_title : null ).'"placeholder="Event Title"/>
                  <span id="generror"></span>
            </div>
            <div class="input-text" id="basicExample">
                  
                    <input type="text" class="custom_date" name="to_date" placeholder="From Date" id="fdate" value="'.( isset( $_POST['to_date'] ) ? $eve_tdate : null ).'"/>
                    <input type="text" class="time start" id="ftime"  placeholder="Start Time"/>
                    <input type="text" class="custom_date" name="to_date" placeholder="To Date" id="tdate" value="'.( isset( $_POST['to_date'] ) ? $eve_tdate : null ).'"/>
                    <input type="text" class="time end" id="ttime" placeholder="End Time"/>
                    

                <script>
                    // initialize input widgets first
                      $("#basicExample .time").timepicker({
                          "showDuration": true,
                          "timeFormat": "g:ia"
                      });

                      // initialize datepair
                      var basicExampleEl = document.getElementById("basicExample");
                      var datepair = new Datepair(basicExampleEl);
                </script>
                <span id="doperror"></span>
            </div>
            <div class="input-text">
                <label></label>
                <input type="checkbox" name="product" />All Day Event &nbsp;&nbsp;
                <input type="checkbox" name="product" />Repeat<br />
                <span id="proerror"></span>
            </div>
            <div class="input-text">
                <input type="text" id="venue" onblur="lnCheck()" placeholder="Venue" value="'.( isset( $_POST['venue'] ) ? $eve_venue : null ).'"/><br />
                <span id="lnerror"></span>
            </div>
            <div class="input-text">
                  <select id="multiple-select-box" class="selectivity-input" data-placeholder="Add Guests" name="traditional[multiple]" multiple >';
                        global $wpdb;
                        /* wpdb class should not be called directly.global $wpdb variable is an instantiation of the class already set up to talk to the WordPress database */ 
                        $result = $wpdb->get_results( "SELECT * FROM wp_users "); /*mulitple row results can be pulled from the database with get_results function and outputs an object which is stored in $result */
                        foreach($result as $row)
                        {
                              echo '<option value="'.$row->user_email.'">'.$row->display_name.'</option>';
                        }
                  echo '</select>
                  
                  <span id="fnerror"></span>
            </div>
            
            <div class="input-text">
                <textarea placeholder="Event Description" rows="6" cols="100"></textarea>
                <span id="doperror"></span>
            </div>
            <div class="input-text">
                <button id="submit">Create Event</button>
            </div>
    
    </form>
    ';
}