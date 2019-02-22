<?php

include_once 'keys.php';
include_once 'Bonanza.php';
include_once 'update_qty.php';

$Bonanza = new Bonanza();
$Bonanza->setCredential($dev_key , $cert_key);

try{
	//$result = $Bonanza->fetchToken();
	
	$result = updateBonanzaInventory(array(array('sku' => 'SRN-SAM-338-3' , 'qty' => 69)));
	
	
	print_r($result);
}
catch(Exception $e){
	print_r($e->getMessage());
}