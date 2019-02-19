<?php
require '../config.php';
require __DIR__ . '/vendor/autoload.php';
define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
define('CREDENTIALS_PATH', '~/.credentials/calendar-php-quickstart.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
$id = $_SESSION['id'];
$table = ($_SESSION['user_id'])? 'inv_users': 'admin';
if ($_GET['id'] && $_GET['table']) {
	$id = $_GET['id'];
	$table = $_GET['table'];
}
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/calendar-php-quickstart.json
define('SCOPES', implode(' ', array(
	Google_Service_Calendar::CALENDAR)
));

function getAuthUrl($client) {

  // Load previously authorized credentials from a file.
	$credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
	if (file_exists($credentialsPath)) {
		$accessToken = file_get_contents($credentialsPath);
	} else {
    // Request authorization from the user.
		$authUrl = $client->createAuthUrl();
		header("Location:$authUrl");
		exit;
	}
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

function verifyToken ($accessToken, $client) {
	global $db;
	global $id;
	global $table;
	$token = json_decode($accessToken);
	$url = 'https://www.googleapis.com/oauth2/v3/tokeninfo?access_token=' . $token->access_token;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output = json_decode(curl_exec($ch));
	curl_close($ch);

	if ($output->error_description == 'Invalid Value') {
		if (!isset($_GET['auth'])) {			
			$db->db_exec("UPDATE $table SET  g_access_token = '' WHERE id = '$id'");
			echo json_encode(array('error' => '1'));
			exit;
		}
		getAuthUrl($client);
	} else {
		$db->db_exec("UPDATE $table SET g_access_token = '$accessToken' WHERE id = '$id'");
	}
}

function getClient($accessToken, $client) {

	$client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
	if ($client->isAccessTokenExpired()) {

		$token = $client->getAccessToken();
		$authObj = json_decode($token);

		if(isset($authObj->refresh_token)) {
			save_refresh_token();
			$client->refreshToken($authObj->refresh_token);
		}

		//$client->refreshToken($client->getRefreshToken());
		$accessToken = $client->getAccessToken();
	}
	verifyToken($accessToken, $client);
}

$client = new Google_Client();
$client->setApplicationName(APPLICATION_NAME);
$client->setScopes(SCOPES);
$client->setAuthConfigFile(CLIENT_SECRET_PATH);
$client->setAccessType('offline');
$client->addScope("email");

$code = $_GET['code'];

$accessToken = $db->func_query_first_cell("SELECT g_access_token FROM $table WHERE id = '$id'");

if (!$code && !$accessToken) {
	if (!isset($_GET['auth'])) {
		$db->db_exec("UPDATE $table SET  g_access_token = '' WHERE id = '$id'");
		echo json_encode(array('error' => '1'));
		exit;
	}
	getAuthUrl($client);
} else if ($code) {
	$accessToken = $client->authenticate($code);
}

getClient($accessToken, $client, $table);

$userInfo = new Google_Service_Oauth2($client);


$user = $userInfo->userinfo->get();
$email = $db->func_query_first_cell("SELECT gmail FROM $table WHERE id = '$id'");

if ($email != $user->email) {
	$db->db_exec("UPDATE $table SET  g_access_token = '' WHERE id = '$id'");

	$adminEmail = $db->func_query("SELECT gmail FROM `admin` WHERE gmail = '". $user->email ."'");

	$userEmail = $db->func_query("SELECT gmail FROM `inv_users` WHERE gmail = '". $user->email ."'");
	if (!$adminEmail && !$userEmail) {
		$client->revokeToken();
	}
	$_SESSION['error'] = ($email)? 'Please use your employee account ('. $email .') to Login.': 'Ask your admin to allow you an email to use with google';
	header("Location:$host_path");
	exit;
}

if ($code && $email == $user->email) {
	$_SESSION['error'] = 'User authenticated!';
	header("Location:$host_path");
}


if ($_GET['updateEvent']) {

	//print_r($_POST);exit;

	$service = new Google_Service_Calendar($client);

	$eventID = $_POST['eventId'];
	// First retrieve the event from the API.
	$event = $service->events->get('primary', $eventID);

	if ($_POST['summary']) {
		$event->setSummary($_POST['summary']);
	}

	if ($_POST['description']) {
		$event->setDescription($_POST['description']);
	}

	if ($_POST['location']) {
		$event->setLocation($_POST['location']);
	}

	if ($_POST['end']) {
		$end = new Google_Service_Calendar_EventDateTime();
		$end->setDateTime(date('c', strtotime($_POST['end'])-60*60*12));  
		$event->setEnd($end);
	}

	if ($_POST['start']) {
		$start = new Google_Service_Calendar_EventDateTime();
		$start->setDateTime(date('c', strtotime($_POST['start'])-60*60*12));  
		$event->setStart($start);
	}


	$updatedEvent = $service->events->update('primary', $event->getId(), $event);

	// Print the updated date.
	if ($updatedEvent->getUpdated()) {
		echo json_encode(array('success' => 1, 'date' => $_POST['end']));
	} else {
		echo json_encode(array('error' => 1));
	}
	exit;
}

if ($_GET['deleteEvent']) {
	$service = new Google_Service_Calendar($client);
	$eventID = $_POST['eventId'];
	$service->events->delete('primary', $eventID);
}

if ($_GET['getEvent']) {
	//print_r($_POST);exit;

	$service = new Google_Service_Calendar($client);

	$eventID = $_GET['eventid'];
	// First retrieve the event from the API.
	$event = $service->events->get('primary', $eventID);
	$start = (empty($event->start->dateTime))? $event->start->date: $event->start->dateTime;
	$end = (empty($event->end->dateTime))? $event->end->date: $event->end->dateTime;
	$json = array(
		'eventId' => $event->getId(),
		'title' => $event->getSummary(),
		'description' => $event->getDescription(),
		'location' => $event->getLocation(),
		'start' => $start,
		'end' => $end
		);

	echo json_encode($json);
	exit;
}


if ($_GET['addEvent']) {
	$service = new Google_Service_Calendar($client);

	$event = new Google_Service_Calendar_Event(array(
		'summary' => $_POST['summary'],
		'location' => $_POST['where'],
		'description' => $_POST['description'],
		'start' => array(
			'dateTime' => date('c', strtotime($_POST['start']))
			),
		'end' => array(
			'dateTime' => date('c', strtotime($_POST['end']))
			),
		'reminders' => array(
			'useDefault' => TRUE
			),
		));

	$calendarId = 'primary';
	$event = $service->events->insert($calendarId, $event);



	// Print the updated date.
	echo json_encode(array('success' => 1, 'event' => $event->getId()));
	exit;
}

if ($_GET['getList']) {
	$service = new Google_Service_Calendar($client);
	$fDate = new DateTime('first day of this month');
	$lDate = new DateTime('last day of this month');

	$calendarId = 'primary';
	$optParams = array(
		'maxResults' => 10,
		'orderBy' => 'startTime',
		'singleEvents' => TRUE,
		'timeMin' => $fDate->format('c'),
		'timeMax' => $lDate->format('c'),
		);

	$results = $service->events->listEvents($calendarId, $optParams);
	$json = array();
	if (count($results->getItems()) != 0) {
		foreach ($results->getItems() as $event) {
			$start = (empty($event->start->dateTime))? $event->start->date: $event->start->dateTime;
			$end = (empty($event->end->dateTime))? $event->end->date: $event->end->dateTime;
			$json[] = array(
				'eventId' => $event->getId(),
				'title' => $event->getSummary(),
				'description' => $event->getDescription(),
				'location' => $event->getLocation(),
				'start' => $start,
				'end' => $end
				);
		}
	}
	//echo '<pre>'; print_r(json_encode($json)); echo '</pre>';
	echo json_encode($json);
	exit;
}