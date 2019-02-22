<?php
set_time_limit(0);
include "../config.php";

if($do_void==true)
{
	$data = json_encode(array('shipmentId'=>$detail['shipstation_shipment_id']));
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://ssapi.shipstation.com/shipments/voidlabel");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	$authtoken = base64_encode("0d50ba42240844269473de9ba065873e:771f86ef07aa47b29e275175d00e6481");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  		"Authorization:Basic $authtoken",
  		"Content-type:application/json"
	));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	$response = curl_exec($ch);
	$response_arr = json_decode($response,true);
	// print_r($response);exit;
	if($response_arr['approved']=='true')
	{
		$is_voided = true;
	}
	else
	{
		$is_voided = false;
	}
	
	curl_close($ch);	
	exit;
}
else
{
	include "../inc/functions.php";
$detail = $db->func_query_first("SELECT * FROM oc_buyback WHERE buyback_id='".(int)$buyback_id."'");
if($detail['customer_id']==0)
					{
						$email = $detail['email'];
						$telephone = $detail['telephone'];
						$firstname = $detail['firstname'];
						$lastname = $detail['lastname'];
						
						$address_1 = $detail['address_1'];
						$city = $detail['city'];
						$postcode = $detail['postcode'];
						$zone_id = $detail['zone_id'];
					}
					else
					{
						
						$customer_detail = $db->func_query_first("SELECT email,telephone FROM oc_customer WHERE customer_id='".$detail['customer_id']."'");
						$address = $db->func_query_first("SELECT * FROM oc_address WHERE address_id='".$detail['address_id']."'");
						
						$email = $customer_detail['email'];
						$telephone = $customer_detail['telephone'];

						if($detail['address_id']!='-1')
						{

						$firstname = $address['firstname'];
						$lastname = $address['lastname'];
						
						$address_1 = $address['address_1'];
						$city = $address['city'];
						$postcode = $address['postcode'];
						$zone_id = $address['zone_id'];
					}
					else
					{
						$firstname = $detail['firstname'];
						$lastname = $detail['lastname'];
						
						$address_1 = $detail['address_1'];

						$city = $detail['city'];
						$postcode = $detail['postcode'];
						$zone_id = $detail['zone_id'];

					}
					}
					$zone = $db->func_query_first_cell("SELECT name FROM oc_zone WHERE zone_id='".(int)$zone_id."'");
//echo $zone;exit;
	$data = json_encode(array(
		'carrierCode'=>$detail['carrier_code'],
		'serviceCode'=>$detail['service_code'],
		'packageCode'=>'package',
		'confirmation'=>'delivery',
		'shipDate'=>date('Y-m-d'),
		'weight'=>array(
						'value'=>$detail['weight'],
						'units'=>'ounces'
			),
		'shipFrom'=>
						array(
								'name'=>'PhonePartsUSA LBB',
								'company'=>'PhonePartsUSA',
								'street1'=>'5145 South Arville Street',
								'street2'=>'Suite A',
								'street3'=>null,
								"city"=> "Las Vegas",
    							"state"=> "NV",
    							"postalCode"=> "89118",
    							"country"=> "US",
    							"phone"=> null,
    							"residential"=> false
							),
		"shipTo"=> 
						array(
		   						"name"=> $firstname.' '.$lastname,
		  						"company"=> null,
		    					"street1"=> $address_1,
		  						"street2"=> null,
		   						"street3"=> null,
		    					"city"=> $city,
		  						"state"=> $zone,
		   						"postalCode"=> $postcode,
		   						"country"=> "US",
		   						"phone"=> $telephone,
		   						"residential"=> false
  							),
		"insuranceOptions"=> null,
  		"internationalOptions"=> null,
  		"advancedOptions"=> null,
  		"testLabel"=> false,
  		"orderId"=>$detail['shipment_number']

		));
// print_r(json_decode($data));exit;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://ssapi.shipstation.com/shipments/createlabel");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	$authtoken = base64_encode("0d50ba42240844269473de9ba065873e:771f86ef07aa47b29e275175d00e6481");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  		"Authorization:Basic $authtoken",
  		"Content-type:application/json"
	));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	$response = curl_exec($ch);
	
	curl_close($ch);
	// print_r($response);exit;
$response_arr = json_decode($response,true);
	if($response_arr['shipmentId'])
	{
		$pdf_base64 = $response_arr['labelData'];
//Get File content from txt file
$pdf_base64_handler = fopen($pdf_base64,'r');
$pdf_content = fread ($pdf_base64_handler,filesize($pdf_base64));
fclose ($pdf_base64_handler);
//Decode pdf content
$pdf_decoded = base64_decode ($pdf_content);
//Write data back to pdf file
$filename = uniqid().'.pdf';
$pdf = fopen ('../../image/labels'.$filename,'w');
fwrite ($pdf,$pdf_decoded);
//close output file
fclose ($pdf);


		$db->db_exec("UPDATE oc_buyback SET pdf_label='$filename',  is_label_created=1,shipstation_shipment_id='".$response_arr['shipmentId']."',tracking_no='".$response_arr['trackingNumber']."' where buyback_id='".(int)$buyback_id."'");
	}
}
?>