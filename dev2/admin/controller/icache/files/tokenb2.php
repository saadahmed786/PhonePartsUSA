<?php

$url = "https://accounts.google.com/o/oauth2/auth";
$client_id = $this->config->get('public_keyg');
$client_secret = $this->config->get('private_keyg');
$refresh_tox = $this->config->get('private_key2');
$redirect_uri = HTTP_CATALOG."admin/controller/icache/tokenb2.php";
$access_type = "offline";
$grant_type = "authorization_code";
$scope = "https://www.googleapis.com/auth/cloudprint";
$params_request = array(
    "response_type" => "code",
    "client_id" => "$client_id",
	"redirect_uri" => "$redirect_uri",
	"refreshToken"=> "$refresh_tox",
    "access_type" => "$access_type",
    "scope" => "$scope"
    );
$request_to = $url . '?' . http_build_query($params_request);

    // try to get an access token

    $url = 'https://accounts.google.com/o/oauth2/token';
    $params = array(
        "client_id" => "$client_id",
		"client_secret" => "$client_secret",
        "redirect_uri" => "$redirect_uri",
		"refresh_token" =>"$refresh_tox",
        "grant_type" => "refresh_token"
    );
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);	
    $json_response = curl_exec($curl);
    curl_close($curl);
    $authObj = json_decode($json_response);

    $access_token = $authObj->access_token;
$text = $access_token;
?>