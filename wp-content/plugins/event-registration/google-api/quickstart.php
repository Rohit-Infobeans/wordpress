<?php
require 'vendor/autoload.php';
include_once($_SERVER['DOCUMENT_ROOT'].'/wordpress/wp-config.php' );
define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
define('CREDENTIALS_PATH', '~/.credentials/calendar-php-quickstart.json');
define('CLIENT_SECRET_PATH', 'client_secret.json');

define('SCOPES', implode(' ', array(
  Google_Service_Calendar::CALENDAR)
));

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfigFile(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');

  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = file_get_contents($credentialsPath);
  } else {
    // Request authorization from the user.
    $authUrl = $client->createAuthUrl();
    printf("Open the following link in your browser:\n%s\n", $authUrl);
    print 'Enter verification code: ';
    $authCode = trim(fgets(STDIN));

    // Exchange authorization code for an access token.
    $accessToken = $client->authenticate($authCode);

    // Store the credentials to disk.
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, $accessToken);
    printf("Credentials saved to %s\n", $credentialsPath);
  }
  $client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->refreshToken($client->getRefreshToken());
    file_put_contents($credentialsPath, $client->getAccessToken());
  }
  return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv("HOMEDRIVE") . getenv("HOMEPATH");
  }
  return str_replace('~', realpath($homeDirectory), $path);
}
//For database values
global $wpdb;
$uid = $_REQUEST['uid'];
$eid = $_REQUEST['eid'];

$result = $wpdb->get_row("Select * ,CONCAT(`event_begin`,'T',`event_stime`) as start, CONCAT(`event_end`,'T',`event_etime`) as end from wp_calendar WHERE event_id='$eid'");
// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);



$attendee = $wpdb->get_row("Select user_email from wp_users where ID='$uid'");
$event = new Google_Service_Calendar_Event(array(
  'summary' => $result->event_title,
  'location' => $result->event_venue,
  'description' => $result->event_desc,
  'start' => array(
    'dateTime' => $result->start,
    'timeZone' => 'UTC +5:30',
  ),
  'end' => array(
    'dateTime' => $result->end,
    'timeZone' => 'UTC +5:30',
  ),
  'recurrence' => array(
    'RRULE:FREQ=DAILY;COUNT=1'
  ),
  
  'attendees' => array(
    array('email' => $attendee->user_email)
  ),
  'reminders' => array(
    'useDefault' => FALSE,
    'overrides' => array(
      array('method' => 'email', 'minutes' => 24 * 60),
      array('method' => 'popup', 'minutes' => 10),
    ),
  ),
));

$calendarId = $attendee->user_email;
$event = $service->events->insert($calendarId, $event);
wp_redirect(site_url().'/index.php/customer-area/events-lists/created-by-me?status=added');
exit;

