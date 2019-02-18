<?php

$production = true;

if($production){
	global $db;
	$configurations = $db->func_query("select * from configuration",'config_key');
	if($configurations){
		define('devID',$configurations['EBAY_DEV_ID']['config_value']);
		define('appID',$configurations['EBAY_APP_ID']['config_value']);
		define('certID',$configurations['EBAY_CERT_ID']['config_value']);
	}
	else{
		define('devID','d79533e4-f531-4b58-9d19-46d1bc199514');
		define('appID','PPUSAc824-7232-4f82-9cbc-84e350e0f0e');
		define('certID','6cccd31f-dbc5-40e1-a07d-1cbd780d4f1b');
	}
	
	//Production
	define('compatabilityLevel' , '837');
	define('serverUrl','https://api.ebay.com/ws/api.dll');
	define('userToken','AgAAAA**AQAAAA**aAAAAA**S3tuWA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AHkYGkDpiBogqdj6x9nY+seQ**3uoCAA**AAMAAA**K+BNCSClTmwqFTy1kfB65bYIOWzt8HfnRzHqyW5gLDsrapkUqZrkdo7mPl7qSNDhx7naJMd56lZckVV9YCzbfJOSdz/X7S+GytD1neheuhVB8gdU8nfsF69vOG81mtMsy2CsXEv/8WOiRicAymH2xp0toxNpu271bEGLIJh56yzWhjB1O2dUO3AoVHFd52flDSgtAoOCkW3KY6kvTy0vpVB9hbUFhmNMCJBmOWyv+MuPITx2BL5d8dZv/LywP0kD1Q+V6aFpYI6AtKsBA+VCOLkfUgq+39Huc1d81IgoMMijb+wHamrvHhcdByWJGliuPa1XY5+cvYirARDZBaksH/K61e2hEj0mOABIYfYUehghehaRWKiSI9jwxCQjDIomZ+1oV/wB/Js8EqU9WuSJz7P6Rkj4QbUfPgbwyfRw9M2ZhXfeUSXGIx7mHU9ryuRcsg1ao4sQX5YbrR+tpfbtehClgiocwGhBKRZ/dkZ6LKQ3oDs94FGyWckxfUCSzNhPuPCJqo8OdBSpmlmAU7E69lEPXPXzvvBq2HaPC1ef4udWgUjFjnnZlTd/jbiLevmtBorzsKwsFNJiBym6mQfJMUQADo00E6kWjdasGVbPzim3jQyTIcOoG+2J6vrmJobkjX8ZctIfEadQ4NEJeaKYchBTMofSpQBqmfNytBIANdtr1dFHuHGFwmVFWXNBdSY7/HrT6aE2i+2h2300FENovKbo9Ct9Joj9lqxlI2anGaIdTDl7YnUKCb4sflqmyg3J');
	define('ebayTokenUrl','https://signin.ebay.com/ws/eBayISAPI.dll');
}
else{
	define('compatabilityLevel' , '837');
	define('devID','c5002c6f-2345-4ac0-9fe0-a4f0dd28e008');
	define('appID','iiabdfd72-5708-4b68-8806-c3a21c50280');
	define('certID','017a947e-fbd7-4b0c-a296-ff63139665da');
	define('serverUrl','https://api.sandbox.ebay.com/ws/api.dll');
	define('ruName','ii-iiabdfd72-5708--bymodl');
	define('userToken','AgAAAA**AQAAAA**aAAAAA**S3tuWA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AHkYGkDpiBogqdj6x9nY+seQ**3uoCAA**AAMAAA**K+BNCSClTmwqFTy1kfB65bYIOWzt8HfnRzHqyW5gLDsrapkUqZrkdo7mPl7qSNDhx7naJMd56lZckVV9YCzbfJOSdz/X7S+GytD1neheuhVB8gdU8nfsF69vOG81mtMsy2CsXEv/8WOiRicAymH2xp0toxNpu271bEGLIJh56yzWhjB1O2dUO3AoVHFd52flDSgtAoOCkW3KY6kvTy0vpVB9hbUFhmNMCJBmOWyv+MuPITx2BL5d8dZv/LywP0kD1Q+V6aFpYI6AtKsBA+VCOLkfUgq+39Huc1d81IgoMMijb+wHamrvHhcdByWJGliuPa1XY5+cvYirARDZBaksH/K61e2hEj0mOABIYfYUehghehaRWKiSI9jwxCQjDIomZ+1oV/wB/Js8EqU9WuSJz7P6Rkj4QbUfPgbwyfRw9M2ZhXfeUSXGIx7mHU9ryuRcsg1ao4sQX5YbrR+tpfbtehClgiocwGhBKRZ/dkZ6LKQ3oDs94FGyWckxfUCSzNhPuPCJqo8OdBSpmlmAU7E69lEPXPXzvvBq2HaPC1ef4udWgUjFjnnZlTd/jbiLevmtBorzsKwsFNJiBym6mQfJMUQADo00E6kWjdasGVbPzim3jQyTIcOoG+2J6vrmJobkjX8ZctIfEadQ4NEJeaKYchBTMofSpQBqmfNytBIANdtr1dFHuHGFwmVFWXNBdSY7/HrT6aE2i+2h2300FENovKbo9Ct9Joj9lqxlI2anGaIdTDl7YnUKCb4sflqmyg3J');
	define('ebayTokenUrl','https://signin.sandbox.ebay.com/ws/eBayISAPI.dll');
}

?>