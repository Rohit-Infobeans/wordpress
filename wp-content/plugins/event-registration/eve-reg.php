<?php
/*
  Plugin Name: Custom Registration
  Plugin URI: http://code.tutsplus.com
  Description: Updates user rating based on number of posts.
  Version: 1.0
  Author: Agbonghama Collins
  Author URI: http://tech4sky.com
 */

 //Used for including js
function wptuts_scripts_load_cdn()
{
    // Register the library again from Google's CDN
    wp_register_script( 'jquery', 'http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js', array(), null, false );
    wp_register_style( 'jqueryui', 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css', array(), '1.4' );
    
    // Register the script like this for a plugin:
    wp_register_script( 'ui-script', plugins_url( 'js/jqueryui.js', __FILE__ ), array( 'jquery' ) );
    wp_register_script( 'custom-script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
    
    wp_register_style( 'eve-style', plugins_url( 'css/eve-reg.css', __FILE__ ) );
   
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'ui-script' );
    wp_enqueue_script( 'custom-script' );
    
    wp_enqueue_style( 'eve-style' );
}
add_action( 'wp_enqueue_scripts', 'wptuts_scripts_load_cdn' );

 function registration_form( $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio ) 
 {
       
    
 
    echo '
    <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
            <div class="input-text">
                  <label>Event Title</label>
                  <input type="text" id="eventtitle"onblur=""/>
                  <span id="generror"></span>
            </div>
            <div class="input-text">
                  <div class="checkout-buttons clearfix">  
                      <input type="text" id="datepicker" />
                      
                      <label for="pickuptime">Choose a pick-up time:</label>
                      <input type="text" id="pickuptime" name="attributes[Pickup_Time]" value="" />
                  </div>
                <span id="doperror"></span>
            </div>
            <div class="input-text">
                <label></label>
                <input type="checkbox" name="product" />All Day Event &nbsp;&nbsp;
                <input type="checkbox" name="product" />Repeat<br />
                <span id="proerror"></span>
            </div>
            <div class="input-text">
                <label for="ln">Venue</label>
                <input type="text" id="ln" onblur="lnCheck()" /><br />
                <span id="lnerror"></span>
            </div>
            <div class="input-text">
                  <label for="fn">Select Users</label>
                  <select>';
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
                <label>Date of Purchase</label>
                <input type="text" name="date" id="date" placeholder="DD/MM/YYYY" onkeyup="checkDate()" />
                <span id="doperror"></span>
            </div>
            <div class="input-text">
                <label>Email</label>
                <input type="text" id="email"onblur="mailCheck()"/>
                <span id="mailerror"></span>
            </div>
            <div class="input-text">
                <button>Create Event</button>
            </div>

    
    </form>
    ';
}

function registration_validation( $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio )  
{
      global $reg_errors;
      $reg_errors = new WP_Error;
      if ( empty( $username ) || empty( $password ) || empty( $email ) ) 
      {
            $reg_errors->add('field', 'Required form field is missing');
      }
      if ( 4 > strlen( $username ) ) 
      {
            $reg_errors->add( 'username_length', 'Username too short. At least 4 characters is required' );
      }
      if ( username_exists( $username ) )
      {
            $reg_errors->add('user_name', 'Sorry, that username already exists!');
      }
      if ( ! validate_username( $username ) ) 
      {
            $reg_errors->add( 'username_invalid', 'Sorry, the username you entered is not valid' );
      }
      if ( 5 > strlen( $password ) ) 
      {
            $reg_errors->add( 'password', 'Password length must be greater than 5' );
      }
      if ( is_wp_error( $reg_errors ) ) 
      {
 
            foreach ( $reg_errors->get_error_messages() as $error ) 
            {     
                    echo '<div>';
                    echo '<strong>ERROR</strong>:';
                    echo $error . '<br/>';
                    echo '</div>';               
            }
      }
}
function complete_registration() {
    global $reg_errors, $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
    if ( 1 > count( $reg_errors->get_error_messages() ) ) {
        $userdata = array(
        'user_login'    =>   $username,
        'user_email'    =>   $email,
        'user_pass'     =>   $password,
        'user_url'      =>   $website,
        'first_name'    =>   $first_name,
        'last_name'     =>   $last_name,
        'nickname'      =>   $nickname,
        'description'   =>   $bio,
        );
        $user = wp_insert_user( $userdata );
        echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';   
    }
}
function custom_registration_function() {
    if ( isset($_POST['submit'] ) ) {
        registration_validation(
        $_POST['username'],
        $_POST['password'],
        $_POST['email'],
        $_POST['website'],
        $_POST['fname'],
        $_POST['lname'],
        $_POST['nickname'],
        $_POST['bio']
        );
         
        // sanitize user form input
        global $username, $password, $email, $website, $first_name, $last_name, $nickname, $bio;
        $username   =   sanitize_user( $_POST['username'] );
        $password   =   esc_attr( $_POST['password'] );
        $email      =   sanitize_email( $_POST['email'] );
        $website    =   esc_url( $_POST['website'] );
        $first_name =   sanitize_text_field( $_POST['fname'] );
        $last_name  =   sanitize_text_field( $_POST['lname'] );
        $nickname   =   sanitize_text_field( $_POST['nickname'] );
        $bio        =   esc_textarea( $_POST['bio'] );
 
        // call @function complete_registration to create the user
        // only when no WP_error is found
        complete_registration(
        $username,
        $password,
        $email,
        $website,
        $first_name,
        $last_name,
        $nickname,
        $bio
        );
    }
 
    registration_form(
        $username,
        $password,
        $email,
        $website,
        $first_name,
        $last_name,
        $nickname,
        $bio
        );
}
// Register a new shortcode: [cr_custom_registration]
add_shortcode( 'cr_custom_registration', 'custom_registration_shortcode' );
 
// The callback function that will replace [book]
function custom_registration_shortcode() {
    ob_start();
    custom_registration_function();
    return ob_get_clean();
}