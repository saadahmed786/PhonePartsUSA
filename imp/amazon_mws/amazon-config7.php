<?php
/**
 * Copyright 2013 CPI Group, LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 *
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */


$store['myStorePGMX']['merchantId'] = 'A5Z18TAP43UVA';//Merchant ID for this store
$store['myStorePGMX']['marketplaceId'] = 'A1AM78C64UM0Y8'; //Marketplace ID for this store
$store['myStorePGMX']['keyId'] = 'AKIAIVKN4QTZIKZTOCTA'; //Access Key ID
$store['myStorePGMX']['secretKey'] = '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'; //Secret Access Key for this store

//Service URL Base
//Current setting is Europe
$AMAZON_SERVICE_URL = 'https://mws-eu.amazonservices.com/';

//Location of log file to use
$logpath = __DIR__.'/log.txt';

//Name of custom log function to use
$logfunction = '';

//Turn off normal logging
$muteLog = false;

?>
