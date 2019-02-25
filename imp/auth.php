<?php

$path = "/home/phonerep/public_html/imp/";

if ($_SERVER ['HTTP_HOST'] == 'localhost') {
	$path = "/opt/lampp/htdocs/ppusa/imp/";
}

require_once ($path . "config.php");

if (! isset ( $_SESSION ['email'] ) || ! $_SESSION ['email']) {
	$_SESSION ['error'] = "Please login to access other pages";
	
	header ( "Location:".$host_path."index.php" );
	exit ();
}

foreach ( $_POST as $key => $value ) {
	if (is_string ( $value )) {
		$_POST [$key] = addslashes ( $value );
	}
}

foreach ( $_GET as $key => $value ) {
	if (is_string ( $value )) {
		$_GET [$key] = addslashes ( $value );
	}
}

$usps_link = 'USPS Tracking:<a href="https://tools.usps.com/go/TrackConfirmAction?tLabels=%s">%s</a>';
$ups_link = 'UPS Tracking:<a href="http://wwwapps.ups.com/WebTracking/processRequest?HTMLVersion=5.0&Requester=NES&AgreeToTermsAndConditions=yes&loc=en_US&tracknum=%s">%s</a>';
$fedex_link = 'Fedex Tracking:<a href="https://www.fedex.com/apps/fedextrack/?action=track&language=english&tracknumbers=%s">%s</a>';