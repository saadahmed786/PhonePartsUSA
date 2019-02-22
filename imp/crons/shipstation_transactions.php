<?php
set_time_limit(0);
include "../config.php";
include "../inc/functions.php";
require_once("../easypost/lib/easypost.php");
\EasyPost\EasyPost::setApiKey(EASYPOST_API);

$last_cron_time = $db->func_query_first_cell("select config_value from configuration where config_key = 'SHIPSTATION_TRANSACTION_LAST_TIME'");

getOrderShipments($last_cron_time , 1);

function getOrderShipments($start_date , $page = 1, $page_size = 100){
	global $db;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://ssapi.shipstation.com/shipments?createDateStart=".urlencode($start_date)."&page=$page");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	$authtoken = base64_encode("0d50ba42240844269473de9ba065873e:771f86ef07aa47b29e275175d00e6481");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  		"Authorization:Basic $authtoken"
	));

	$response = curl_exec($ch);
//echo $response;exit;
	if($response){
		$response = json_decode($response,true);
		$createDate = '';
		foreach($response['shipments'] as $shipment){
			if($shipment['isReturnLabel']=='true')
				{
					// continue;
					$rma_check = $db->func_query_first_cell("SELECT rma_number FROM inv_returns WHERE email='".$shipment['customerEmail']."' and rma_status<>'Completed' order by 1 desc");
					if($rma_check)
					{
						$shipment['orderNumber'] = $rma_check;
					}
					else
					{
						$rma_check = $db->func_query_first_cell("SELECT shipment_number FROM oc_buyback WHERE email='".$shipment['customerEmail']."' and status<>'Completed' order by 1 desc");	

						if($rma_check)
						{
							$shipment['orderNumber'] = $rma_check;
						}
					}
				}


			$isExist = $db->func_query_first_cell("select id from inv_shipstation_transactions where tracking_number = '".$shipment['trackingNumber']."'");
			if(!$isExist){
				$order_shipment = array();
				$order_shipment['shipment_id'] = $shipment['shipmentId'];
				$order_shipment['order_id']    = $shipment['orderNumber'];
				$order_shipment['shipping_cost'] = $shipment['shipmentCost'];
				$order_shipment['ship_date'] = $shipment['shipDate'];
				$order_shipment['insurance_cost']  = $shipment['insuranceCost'];
				$order_shipment['tracking_number'] = $shipment['trackingNumber'];
				$order_shipment['carrier_code'] = $shipment['carrierCode'];
				$order_shipment['service_code'] = $shipment['serviceCode'];
				$order_shipment['package_code'] = $shipment['packageCode'];
				$order_shipment['confirmation'] = $shipment['confirmation'];
				$order_shipment['voided'] = $shipment['voided'];
				$order_shipment['weight'] = $shipment['weight']['value'];
				$order_shipment['units']  = $shipment['weight']['units'];
				$order_shipment['date_added'] = date("Y-m-d H:i:s");

				$order_check = $db->func_query_first_cell("SELECT order_id FROM inv_orders WHERE order_id='".$shipment['orderNumber']."'");

				if($order_check)
				{
					$order_shipment['is_mapped']=1;
				}
				else
				{
					$order_check = $db->func_query_first_cell("SELECT order_id FROM inv_orders WHERE order_id='E".$shipment['orderNumber']."'");

					if($order_check)
				{
					$order_shipment['order_id']    = 'E'.$shipment['orderNumber'];
					$order_shipment['is_mapped']=1;
				}
				else
				{
					$order_check = $db->func_query_first_cell("SELECT order_id FROM inv_orders WHERE order_id='RL".$shipment['orderNumber']."'");

						if($order_check)
				{
					$order_shipment['order_id']    = 'RL'.$shipment['orderNumber'];
					$order_shipment['is_mapped']=1;
				}
				else
				{
					$order_shipment['is_mapped']=0;
				}
					
				}
				}
				
				$db->func_array2insert("inv_shipstation_transactions", $order_shipment);

					if($shipment['voided']==0 && $order_shipment['is_mapped']==1)
					{
						
					$order_detail = getOrder($order_shipment['order_id']);
							
						

					$accounts = array();
					$accounts['description'] = stripDashes($order_shipment['service_code']).' Shipping #'.$order_detail['order_id'];
					$accounts['debit'] = 0.00;
					$accounts['credit'] = $order_detail['shipping_cost'];
					$accounts['order_id'] = $order_detail['order_id'];
					$accounts['customer_email'] = $order_detail['email'];
					$accounts['type'] = 'sales';
					$accounts['contra_account_code']='shipping_paid';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					//add_accounting_voucher($accounts); // store credit applied


					$accounts = array();
					$accounts['description'] = stripDashes($order_shipment['service_code']).' Shipping #'.$order_detail['order_id'];
					$accounts['debit'] = $order_detail['shipping_cost'];
					$accounts['credit'] = 0.00;
					$accounts['order_id'] = $order_detail['order_id'];
					$accounts['customer_email'] = $order_detail['email'];
					$accounts['type']='shipping_paid';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					//add_accounting_voucher($accounts); // store credit applied



				}

				if($order_shipment['voided']==0)
				{

					$accounts = array();
					$accounts['description'] = stripDashes($order_shipment['service_code']).' Shipping Cost #'.$shipment['trackingNumber'];
					$accounts['debit'] = $shipment['shipmentCost'];
					$accounts['credit'] = 0.00;
					$accounts['order_id'] = $shipment['trackingNumber'];
					// $accounts['customer_email'] = $email;
					$accounts['type']='shipping_expense';
					$accounts['contra_account_code'] = $shipment['carrierCode'];
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // store credit applied


					$accounts = array();
					$accounts['description'] = stripDashes($order_shipment['service_code']).' Shipping Cost #'.$shipment['trackingNumber'];
					$accounts['debit'] = 0.00;
					$accounts['credit'] = $shipment['shipmentCost'];
					$accounts['order_id'] = $shipment['trackingNumber'];
					// $accounts['customer_email'] = $email;
					$accounts['type'] = $shipment['carrierCode'];
					$accounts['contra_account_code']='shipping_expense';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // store credit applied
				}

					

				// Insert EasyPost Tracker
				$tracking_code = $shipment['trackingNumber'];
				//$tracking_code='EZ1000000001';
  $carrier = $shipment['carrierCode'];
// UnComment when funds available
switch($carrier)
{
	case 'express_1':
	case 'endicia':
	case 'stamps_com':
		$_carrier = 'USPS';
	break;
	case 'fedex':
		$_carrier = 'FedEx';
	break;

	case 'ups':
		$_carrier = 'UPS';
	break;
}
  $tracker_obj = \EasyPost\Tracker::create(array('tracking_code' => $tracking_code,'carrier'=>$_carrier));
 $tracker_info = \EasyPost\Tracker::retrieve($tracker_obj->id);

 if($tracker_info->id)
 {
 	$_array = array();
 	$_array['tracker_id'] = $tracker_info->id;
 	$_array['tracking_code'] = $tracker_info->tracking_code;
 	$_array['status'] = $tracker_info->status;
 	$_array['created_at'] = $tracker_info->created_at;
 	$_array['updated_at'] = $tracker_info->updated_at;
 	$_array['weight'] = (float)$tracker_info->weight;
 	$_array['est_delivery_date'] = $tracker_info->est_delivery_date;
 	if($rma_check)
 	{
 	$_array['shipment_id'] = $rma_check;
 		
 	}
 	else
 	{
 	$_array['shipment_id'] = $tracker_info->shipment_id;
 		
 	}
 	$_array['carrier'] = $tracker_info->carrier;
 	
 	
 	$check = $db->func_query_first_cell("SELECT tracker_id FROM inv_tracker WHERE tracker_id='".$tracker_info['id']."'");
 	if($check)
 	{
	$db->func_array2update("inv_tracker",$_array," tracker_id = '".$tracker_info->id."' ");
 	}
 	else
 	{
 		$_array['datetime'] = date('Y-m-d H:i:s');
 		$db->func_array2insert("inv_tracker", $_array);
 	}
 	$db->db_exec("DELETE FROM inv_tracker_status WHERE tracker_id='".$tracker_info->id."'");
 	foreach($tracker_info->tracking_details as $detail)
 	{
 		$_array = array();
 	$_array['tracker_id'] = $tracker_info->id;
 	$_array['message'] = $db->func_escape_string($detail->message);
 	$_array['status'] = $db->func_escape_string($detail->status);
 	$_array['datetime'] = $db->func_escape_string($detail->datetime);
 	$_array['tracking_location'] = $db->func_escape_string($detail->tracking_location);
	$db->func_array2insert("inv_tracker_status", $_array);

 	}
 }

			}
			
			$createDate = $shipment['createDate'];
		}
		
		if($createDate){
			$createDate = substr($createDate,0,19);
			$createDate = date('Y-m-d H:i:s',strtotime($createDate));
			$db->db_exec("update configuration SET config_value = '$createDate' where config_key = 'SHIPSTATION_TRANSACTION_LAST_TIME'");
		}
		
		if($response['pages'] > $page){
			 getOrderShipments($start_date , $page + 1);
		}
		
		return;
	}
	else{
		$error = curl_error($ch);
		print_r(curl_getinfo($ch));
	}
	curl_close($ch);
}

echo "success";