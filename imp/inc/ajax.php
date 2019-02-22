<?php

include_once '../config.php';
include_once 'functions.php';

if(!isset($_SESSION['list'])){
	$_SESSION['list'] = array();
}

if($_GET['action'] == 'addToList'){
	if($_GET['product_id']){
		$_SESSION['list'][$_GET['product_id']] = array("qty"=>(int)$_GET['qty']);
		echo "Success";
	}
	else{
		echo "Error: Product ID is not valid";
	}
}
elseif($_GET['action'] == 'removeFromList'){
	if($_GET['product_id'] || $_GET['is_new'] == 1){
		if($_GET['is_new'] == 1){
			unset($_SESSION['newlist'][$_GET['product_id']]);
		}
		else{
			unset($_SESSION['list'][$_GET['product_id']]);
		}

		$db->db_exec("DELETE FROM inv_shipment_items where shipment_id='".(int)$_GET['shipment_id']."' and product_sku='".$db->func_escape_string($_GET['sku'])."'");
			 addComment('shipment',array('id' => $_GET['shipment_id'], 'comment' => $_GET['sku'].' is removed from the shipment'));
		echo "Success";
	}
	else{
		echo "Error: Product ID is not valid";
	}
}