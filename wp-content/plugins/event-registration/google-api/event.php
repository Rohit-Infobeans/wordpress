<?php
error_reporting(E_ALL);
require_once 'src/Google/Client.php';
require_once 'src/Google/Service/Calendar.php';
//date_default_timezone_set('Asia/Calcutta');




$client = new Google_Client();
$client->setApplicationName("Google Calendar PHP Starter Application");
$client->setClientId('set client di');
$client->setClientSecret('client secret here');
$client->setRedirectUri('Page url Where you want to response back');
$client->setDeveloperKey('Developer key');
$cal = new Google_CalendarService($client);


if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken())
{

  $event = new Google_Event();
  $event->setSummary('summary');
  $event->setDescription('summary');
  $event->setLocation('location');
  $start = new Google_EventDateTime();
 // $orderdateS_T=$_SESSION['orderdate']."T01:00:00+05:30";
  $start->setDateTime('Sorderdate');
  $event->setStart($start);
  $end = new Google_EventDateTime();
  //$orderdateE_T=$_SESSION['orderdate']."T02:00:00+05:30";
  $end->setDateTime('Eorderdate');
  $event->setEnd($end);
  $createdEvent = $cal->events->insert('Calendar ID COme Here', $event);
  
  echo 'Event Created Successfully';
} 
else 
{

  $authUrl = $client->createAuthUrl();
  print "<hr><br><font size=+2><a href='$authUrl'>Connect Me!</a></font>";

}


?>