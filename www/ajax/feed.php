<?php
/**
 * Récupère les données pour le mois actif
 * php version 7.2
 * 
 * @category REST
 * @package  Project
 * @author   Serge NOEL <serge.noel@easylinux.fr>
 * @license  GNU GPL
 * @link     http://www.easylinux.fr/calendar
 */
require '../vendor/autoload.php';

/**
 * Returns an authorized API client.
 * 
 * @return object Google_Client the authorized client object
 */
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Easy Calendar');
    $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
    $client->setAuthConfig('../config/credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = '../config/token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}


// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);

$calendarId = 'sujvsvr51d6ueir37kd8ntm2ng@group.calendar.google.com';
$optParams = array(
  'maxResults' => 100,
  'orderBy' => 'startTime',
  'singleEvents' => true,
  'timeMin' => $_POST["start"],
  'timeMax' => $_POST["end"]
);
$results = $service->events->listEvents($calendarId, $optParams);
$events = $results->getItems();


if (empty($events)) {
    $oEvents=[];
} else {
    foreach ($events as $event) {
        $oEvents[]=[
          'id' => $event->getId(),
          'title' => $event->getSummary(),
          'start' => $event->start->dateTime
        ];
    }
}

echo json_encode($oEvents);

