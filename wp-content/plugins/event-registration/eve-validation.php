<?php
function registration_validation( $eve_title, $eve_sdate, $eve_stime, $eve_tdate, $eve_ttime, $eve_venue, $eve_users)  
{
      global $reg_errors;
      $reg_errors = new WP_Error;
      if ( empty( $eve_title ) || empty( $eve_sdate ) || empty( $eve_tdate ) || empty( $eve_venue )) 
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
                    echo '<div>';
                    echo '<strong>ERROR</strong>:';
                    echo $error . '<br/>';
                    echo '</div>';               
            }
      }
}