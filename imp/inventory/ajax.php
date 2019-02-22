<?php
require_once("../auth.php");
require_once("../inc/functions.php");
require_once("class.php");
include_once '../inc/split_page_results.php';
require_once('../html2_pdf_lib/html2pdf.class.php');
include("../phpmailer/class.smtp.php");
include("../phpmailer/class.phpmailer.php");
require_once(str_replace('imp/', 'system/', $path) . 'PrintNode-PHP-master/vendor/autoload.php');

// print_r($_POST);exit;
$_settings = oc_config('imp_inventory_setting');
//$data = str_replace("'", "", $data);
$setting = unserialize($_settings);


if(isset($_POST['type']) && $_POST['type']=='ajax')
{
	if(isset($_POST['action']) && $_POST['action']=='load_order')
	{
		$bulk_order_id = '';
		$order_id = $db->func_escape_string($_POST['order_id']);
		if(!isset($_POST['is_adjustment']))
		{
			$_POST['is_adjustment'] = 0;
		}
		$is_adjustment = (int)$_POST['is_adjustment'];
		// $order_ids = $db->func_escape_string($_POST['order_ids']);
		if($order_id=='')
		{
			$check = $inventory->checkBulkOrders($_POST['order_ids']);
			if(!$check)
			{
				$bulk_order_id = $inventory->makeBulkOrder($_POST['order_ids']);
			}
			else
			{
				$bulk_order_id = $check['bulk_number'];
			}
			$ajaxOrderDetail = $inventory->getOrders($_POST['order_ids']);
			// print_r($ajaxOrderDetail);exit;
		}
		else
		{

			$ajaxOrderDetail = $inventory->getOrder($order_id,($is_adjustment?true:false));

			if(!$ajaxOrderDetail)
			{
				$bulk_orders = $inventory->getBulkOrder($order_id);

				if($bulk_orders)
				{
					$ajaxOrderDetail = $inventory->getOrders($bulk_orders['order_ids']);
					$bulk_order_id = $bulk_orders['bulk_number'];
				}
			}
			if($order_id=='562343')
			{
		// print_r($ajaxOrderDetail);exit;
				
			}

		}

		if($ajaxOrderDetail)
		{
			if(isset($_POST['shipping_mapping']))
			{
				// $shipping_map = $inventory->getShippingMap($ajaxOrderDetail['shipping_method'],$ajaxOrderDetail['order_price']);
				// $ajaxOrderDetail['service_map'] = $shipping_map;
			}
			$ajaxOrderDetail['bulk_number'] = $bulk_order_id;

			echo json_encode($ajaxOrderDetail);
			
		}
		else
		{
			$json = array();
			$json['error'] = 1;
			echo json_encode($json);
		}	
		
	}

	if(isset($_POST['action']) && $_POST['action']=='get_cheapest_rate')
	{
		$order_ids = $_POST['order_id'];
		$order_ids = explode(",", $order_ids);
		$order_id = $order_ids[0];
		$weight_lb = (float)$_POST['weight_lb'];
		$weight_oz = (float)$_POST['weight_oz'];
		$weight = (float)$weight_lb * 16;
		$weight = (float)$weight + (float)$weight_oz;
		if($weight<=0 or $order_id=='')
		{
			exit;
		}
		$order = $inventory->getOrder($order_id,false);
		$json = array();
		if(!$order)
		{
			$json['error'] = 1;
		}
		else
		{
			$json['success'] = 1;
			$_carriers = $inventory->listCarriers();
			$_carriers = json_decode($_carriers,true);

			$carriers = array();
			foreach($_carriers['carriers'] as $_carrier)
			{
				$carriers[] = $_carrier['carrier_id'];
			}
			$carrier = '"'.implode('","', $carriers).'"';

			$body = '{
				"shipment": {
					"validate_address": "no_validation",
					"ship_to": {
						"name": "'.$order['shipping_name'].'",
						"phone": "'.$order['telephone'].'",
						"company_name": "'.$order['company'].'",
						"address_line1": "'.$order['address1'].'",
						"city_locality": "'.$order['city'].'",
						"state_province": "'.$order['state_short'].'",
						"postal_code": "'.($order['country_code']=='US'?substr($order['zip'],0,5):$order['zip']).'",
						"country_code": "'.$order['country_code'].'"
					},
					"ship_from": {
						"name": "PhonePartsUSA",
						"phone": "855-213-5588",
						"company_name": "PhonePartsUSA",
						"address_line1": "5145 South Arville Street",
						"address_line2": "Suite A",
						"city_locality": "Las Vegas",
						"state_province": "NV",
						"postal_code": "89118",
						"country_code": "US"
					},';


					$body.='"packages": [
					{
						"weight": {
							"value": '.(float)$weight.',
							"unit": "ounce"
						}
					}
					]
				},
				"rate_options": {
					"carrier_ids": [
					'.$carrier.'
					]
				}
			}';
// echo $body;exit;
			$response = $inventory->getRates($body);
			$response = json_decode($response,true);
		// print_r($response);exit;
			$shipping_maps = $inventory->getShippingMap($order['shipping_method']);
		// print_r($shipping_maps);exit;
			$cheapest_rate = 9999;
			$service_code = '';
			$package_type = '';

			foreach($response['rate_response']['rates'] as $rate)
			{

				if($rate['carrier_code']!='stamps_com')
				{
					$package='';
				}
				else
				{
					$package = 'package';
				}
				
				// if(trim($service)!=trim($rate['service_code']) or $rate['package_type']!=$package)
				if(!in_array($rate['service_code'], $shipping_maps) or $rate['package_type']!=$package )
				{
					// if($rate['carrier_code']!='fedex' && $rate['package_type']!='package')
					// {
					continue;

					// }
				}
				else
				{

					$my_rate=$rate['shipping_amount']['amount'];
					$my_rate+=$rate['insurance_amount']['amount'];
					$my_rate+=$rate['confirmation_amount']['amount'];
					$my_rate+=$rate['other_amount']['amount'];
					// echo $my_rate."-".$rate['service_code'].'-'.$rate['package_type']."\n";
					if($my_rate<$cheapest_rate)
					{
						if($rate['package_type']=='')
						{
							$rate['package_type'] = 'package';
						}	
						$cheapest_rate = $my_rate;
						$json['cheapest_rate'] = $cheapest_rate;
						$json['service_code'] = $rate['carrier_id'].'~'.$rate['service_code'];
						$json['package_type'] = $rate['package_type'];
					}

					
				}		
			}

		// exit;


		}
		echo json_encode($json);

	}



	/*if(isset($_POST['action'])=='mark_picked' && $_POST['action']=='mark_picked')
	{
		// print_r($_POST['sku']);
		$order_id  = $_POST['order_id'];
		$skus = $_POST['sku'];
		$sort_array = array();
		foreach($skus as $sku)
		{
			if(isset($sort_array[$sku]))
			{
				$sort_array[$sku]+=1;
			}
			else
			{
				$sort_array[$sku]=1;
				
			}
		}
		$json = array();
		if($inventory->markPicked($order_id,$sort_array))
		{
			$json['success'] = 1;
			if($inventory->makeLedger($order_id,$sort_array,$_SESSION['user_id'],'picked'))
			{
				$json['success'] = 1;
			}
			else
			{
				$json['success'] = 0;
			}
		}
		else
		{
			$json['success'] = 0;
		}
		echo json_encode($json);
	}
	*/

	if(isset($_POST['action'])=='mark_picked' && $_POST['action']=='mark_picked')
	{
		
		$skus = $_POST['sku'];
		$close_short = $_POST['close_short'];
		

		$sort_array = array();
		foreach($skus as $_sku)
		{
			$data = explode("~", $_sku);
			$order_id = $data[0];
			$sku = $data[1];

			if(isset($sort_array[$order_id][$sku]))
			{
				$sort_array[$order_id][$sku]+=1;
			}
			else
			{
				$sort_array[$order_id][$sku]=1;
				
			}
		}

		$json = array();

		foreach($sort_array as $order_id => $data)
		{
			if($inventory->markPicked($order_id,$sort_array[$order_id]))
			{
				$json['success'] = 1;
				if($inventory->makeLedger($order_id,$sort_array[$order_id],$_SESSION['user_id'],'picked'))
				{
					if($close_short==1)
					{
						$inventory->closeShort($order_id);
						$inventory->mailHelpClosedShort($order_id,MAIL_HOST,MAIL_USER,MAIL_PASSWORD);
						orderTotal($order_id,true);
						
					}
					$json['success'] = 1;
				}
				else
				{
					$json['success'] = 0;
				}
			}
			else
			{
				$json['success'] = 0;
			}
		}
		echo json_encode($json);
		



	}

	// if(isset($_POST['action'])=='save_picked' && $_POST['action']=='save_picked')
	// {
	// 	// print_r($_POST['sku']);
	// 	$order_id  = $_POST['order_id'];
	// 	$skus = $_POST['sku'];
	// 	$sort_array = array();
	// 	foreach($skus as $sku)
	// 	{
	// 		if(isset($sort_array[$sku]))
	// 		{
	// 			$sort_array[$sku]+=1;
	// 		}
	// 		else
	// 		{
	// 			$sort_array[$sku]=1;

	// 		}
	// 	}
	// 	$json = array();
	// 	if($inventory->savePicked($order_id,$sort_array))
	// 	{
	// 		$json['success']=1;


	// 	}
	// 	else
	// 	{
	// 		$json['success'] = 0;
	// 	}
	// 	echo json_encode($json);
	// }

	if(isset($_POST['action']) && $_POST['action']=='save_picked')
	{
		$skus = $_POST['sku'];
		

		$sort_array = array();
		foreach($skus as $_sku)
		{
			$data = explode("~", $_sku);
			$order_id = $data[0];
			$sku = $data[1];

			if(isset($sort_array[$order_id][$sku]))
			{
				$sort_array[$order_id][$sku]+=1;
			}
			else
			{
				$sort_array[$order_id][$sku]=1;
				
			}
		}

		$json = array();

		foreach($sort_array as $order_id => $data)
		{
			if($inventory->savePicked($order_id,$sort_array[$order_id]))
			{
				$json['success'] = 1;

			}
			else
			{
				$json['success'] = 0;
			}
		}
	}

	if(isset($_POST['action']) && $_POST['action']=='print_pick_list'){

		require_once('../inc/barcode/BarcodeGenerator.php');
		require_once('../inc/barcode/BarcodeGeneratorPNG.php');
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

		// echo 'here';exit;
		$bulk_data = $inventory->checkBulkOrders($_POST['order_ids']);


		$order_ids = $inventory->getOrders($_POST['order_ids']);
		$items = array();
		foreach($order_ids['data'] as $order_id)
		{
			foreach($order_id['items'] as $skus)
			{
				if(!isset($items[$skus['sku']]))
				{
					$items[$skus['sku']] = array(
						'sku'=>$skus['sku'],
						'name'=>$skus['name'],
						'quantity'=>$skus['quantity'],
						'image'=>$skus['image']

						);
				}
				else
				{
					$items[$skus['sku']]['quantity'] += $skus['quantity'];
				}
			}
		}
		$html='<page><page_footer>

		<table class="page_footer" align="right">
			<tr>
				<td align="right" style="width: 100%; text-align: right">
					Page [[page_cu]] of [[page_nb]]
				</td>
			</tr>
		</table>
		</page_footer></page>';
		$html.='
		<table  border="0">
			<tr>
				<td style="width:500px"><h1>Pick List ('.$bulk_data['bulk_number'].')</h1></td>

				<td align="right">
					'.date('l F d Y h:i A').'
				</td>
			</tr>

		</table>

		<table border="0" style="width:100%;" cellpadding="0" cellspacing="0" >
			<tr style="font-weight:bold; ">
				<td style="width:80px;height:20px;padding:5px;border-top:2px solid black;border-bottom:2px solid black;"></td>
				<td style="width:100px;padding:5px;border-top:2px solid black;border-bottom:2px solid black;">SKU</td>
				<td style="width:400px;padding:5px;border-top:2px solid black;border-bottom:2px solid black;">Description</td>
				<td style="width:75px;padding:5px;border-top:2px solid black;border-bottom:2px solid black;"># Required</td>

			</tr>';
			$total_required = 0;
			foreach($items as $item)
			{
				$item['image'] = noImage($item['image'],$host_path,$path);
				$html.='<tr style="font-size:12px;border-top:1px solid #000000">
				<td style="height:40px;padding:5px;vertical-align:text-top;b;border-bottom:2px solid black;"><img src="'.$item['image'].'" style="width:55px;height:55px"></td>
				<td style="padding:5px; vertical-align:text-top;b;border-bottom:2px solid black;">'.$item['sku'].'</td>
				<td style="padding:5px;word-wrap: break-word;width:100px;vertical-align:text-top;b;border-bottom:2px solid black;">'.$item['name'].'</td>
				<td style="padding:5px;vertical-align:text-top;text-align:center;b;border-bottom:2px solid black;'.($item['quantity']>1?'font-weight:bold;font-size:16px':'font-size:14px').'">'.$item['quantity'].'</td>

			</tr>';
			$total_required +=(int)$item['quantity'];

		}
		




		$html.='</table>

		

		';
		$html.='<br><table align="right">
		<tr>
			<td align="right" style="width: 100%; text-align: right;font-weight:bold;font-size:16px">Total Items Required: '.$total_required.'</td>
		</tr>
		

	</table>
	<br>
	<img style="width:170px;height:63px;margin-right:140px" src="data:image/png;base64,' . base64_encode($generator->getBarcode($bulk_data['bulk_number'], $generator::TYPE_CODE_128)) . '">
	';
		// echo $html;exit;
	try {



		$html2pdf = new HTML2PDF('P', 'A4', 'en');

		$html2pdf->setDefaultFont('arial');
		$html2pdf->writeHTML($html);

		$filename = '../files/Pick List.pdf';
		$file = $html2pdf->Output($filename, 'F');

	// echo $filename.'.pdf';

	} catch (HTML2PDF_exception $e) {
		echo $e;
		exit;
	}


	if ($setting['download_pdf']) {
  // $credentials = 'f9305047bdf9a187cfc02de4780b8e0c7cb3261a'; /*Dev ID*/
		$credentials = new PrintNode\Credentials();
		$credentials->setApiKey('19982dc5978951c99f98cdcfe5feb4881cc5147b');
		$request = new PrintNode\Request($credentials);
		// $computers = $request->getComputers();
		$printers = $request->getPrinters();
    	// print_r($printers);exit;
		// $printJobs = $request->getPrintJobs();
		$printJob = new PrintNode\PrintJob();
		// $printJob->printer = 130442; //$printers[1]; /*Dev id*/
		// $printJob->printer = 130444; //$printers[1]; /*Dev id*/
		$printJob->printer = 182844;
		$printJob->contentType = 'pdf_base64';
		$printJob->content = base64_encode(file_get_contents($filename));
		$printJob->source = 'My App/1.0';
		$printJob->title = 'Pick List - IMP';
		$response = $request->post($printJob);
		$statusCode = $response->getStatusCode();
		$statusMessage = $response->getStatusMessage();


		$json['success']  = $statusMessage;
		$json['href']  = 0;

	}
	else
	{
		$json['success'] = 1;
		$json['href'] = 1;
	}
	

	echo json_encode($json);exit;




}


if(isset($_POST['action']) && $_POST['action']=='print_pack_list'){

	require_once('../inc/barcode/BarcodeGenerator.php');
	require_once('../inc/barcode/BarcodeGeneratorPNG.php');
	$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

		// echo 'here';exit;
		// $order_id = $_POST['order_id'];
	$detail = $inventory->getOrder($_POST['order_id']);

	$html='<table  border="0">
	<tr>
		<td style="width:100%;width:740px;background-color:#000;font-weight:bold;color:#FFF;text-align:center"><strong>Packing Slip</strong></td>
		
		
	</tr>

</table>
<table>
	<tr>
		<td style="width:550px"><img src="https://phonepartsusa.com/image/logo_new.png"><br><br>5145 South Arville Street Suite A<br>Las Vegas, NV 89118
		</td>
		<td align="right" style="vertical-align:text-top" ><span style="font-weight:bold">PhonePartsUSA.com</span><br>
			help@phonepartsusa.com<br>
			(855) 213­5588<br>
			Mon-Fri 10am - 4pm PST
		</td>
	</tr>
</table>
<br>
<table border="0" style="" >
	<tr>
		<td style="font-weight:bold;width:80px">Ship To:</td>
		<td style="width:380px">'.$detail['customer_name'].'</td>

		<td style="font-weight:bold">Order #</td>
		<td style="font-weight:bold">'.$detail['order_id'].'</td>


	</tr>

	<tr>
		<td style="font-weight:bold"></td>
		<td>'.$detail['address1'].'</td>

		<td style="font-weight:bold">Date</td>
		<td style="">'.$detail['order_date'].'</td>


	</tr>

	<tr>
		<td style="font-weight:bold"></td>
		<td>'.$detail['city'].', '.$detail['state'].', '.$detail['zip'].','.$detail['country'].'.</td>

		<td style="font-weight:bold">User</td>
		<td style="">'.$detail['email'].'</td>


	</tr>

	<tr>
		<td style="font-weight:bold"></td>
		<td></td>

		<td style="font-weight:bold">Telephone</td>
		<td style="">'.$detail['telephone'].'</td>


	</tr>


</table>
<br>

<table border="0" style="width:730px;" cellpadding="0" cellspacing="0" >
	<tr style="font-weight:bold;background-color:#000;color:#FFF; ">
		
		<td style="width:140px;">Item</td>
		<td style="width:480px;">Description</td>
		<td style="width:110px;text-align:center">Qty</td>

	</tr>';
	$total_required = 0;
	foreach($detail['items'] as $item)
	{
		$html.='<tr style="">
		
		<td style="vertical-align:text-top;b;border-bottom:2px solid black;">'.$item['sku'].'</td>
		<td style="word-wrap: break-word;vertical-align:text-top;b;border-bottom:2px solid black;">'.(strlen($item['name']) > 70 ? substr($item['name'],0,70)."..." : $item['name']).'</td>
		<td style=";vertical-align:text-top;text-align:center;b;border-bottom:2px solid black;'.($item['quantity']>1?'font-weight:bold;font-size:14px':'font-size:12px').'">'.$item['quantity'].'</td>

	</tr>';
	$total_required +=(int)$item['quantity'];

}





$html.='</table>



';
$html.='<br><table align="right">
<tr>
	<td align="right" style="width: 100%; text-align: right;font-weight:bold;font-size:14px">Total Item(s): '.$total_required.'</td>
</tr>


</table>

<br>
Customer Comments: 
<br><br>
'.$detail['shipping_method'].'
<br>
<img style="width:170px;height:63px;margin-right:140px" src="data:image/png;base64,' . base64_encode($generator->getBarcode('S'.$detail['order_id'], $generator::TYPE_CODE_128)) . '">

<img style="width:170px;height:63px" src="data:image/png;base64,' . base64_encode($generator->getBarcode($detail['order_id'], $generator::TYPE_CODE_128)) . '">

<table>
	<tr>
		<td style="width:365px;height:373px;vertical-align:text-top;" >
			<div style="font-size:10px;padding:3px;border:1px solid #000;padding-bottom:20px">
				<span style="font-size:11px;font-weight:bold">Return Policy</span><br>
				<br>
				We offer our customers 60 days to return all undamaged and untampered parts. To begin a return please select the EZ Returns link on the footer of PhonePartsUSA.com. In this form, you may select the items you wish to return, the reason for the return and how you would like us to process the return (refund, store credit or replacement). Upon completion, print out and affix the RMA label on the return package. <span style="font-weight:bold">NOTE:</span> Returns mailed without a RMA label are subject to longer processing times. All items which are not linked to RMAs (returned by mail or at our store-front) must be returned by the original purchaser (business account or indvidual account)

				<br>
				<br>
				<span style="font-size:11px;font-weight:bold">0-30 Days after order date:</span><br>
				All returned items in their original condition are eligible for full refund, store credit or replacement. Products returned in a used or non-original condition are subject to a 20-50% restocking fee. Shipping is non-refundable. 

				<br>
				<br>
				<span style="font-size:11px;font-weight:bold">31-60 Days after order date:</span><br>
				All returned items in their original condition are eligible for store credit or replacement. The store credit amount will reflect on the current selling price or purchase price, whichever is lower. Products returned in a used or non-original condition are subject to a 20-50% re-stocking fee. Shipping is non-refundable. 
				<br><br>

				<span style="font-size:11px;font-weight:bold">61+ Days after order date</span><br>
				We do not accept returns past 61 days. 



			</div>

		</td>
		<td style="width:365px;vertical-align:text-top">
			<div style="font-size:10px;padding:3px;border:1px solid #000;margin-bottom:3px">
				<span style="font-size:11px;font-weight:bold">Shipping Damages</span><br>
				<br>
				Customers are to report all shipping related damages within 5 business days of the shipping carrier delivery date. All damaged items must be photographed and emailed to our support team; <span style="font-weight:bold">help@phonepartsusa.com</span>. The damaged product must be mailed back to our facility. Once received, the parts will be inspected by our Returns Department. If the damages are found to be shipping related, appropriate shipping and purchase cost reimbursement will be issued via store credit or product replacement. 

			</div>



			<div style="font-size:10px;padding:3px;border:1px solid #000;margin-bottom:3px">
				<span style="font-size:11px;font-weight:bold">Warranty</span><br>
				<br>
				PhonePartsUSA offers a 60 day (lifetime for approved business customers) warranty on all parts and accessories purchased from our 
				website. This warranty applies to all part defects. <span style="text-decoration:underline">Part tampering  
				and/or installation damage void eligibility. </span>


			</div>

			<div style="font-size:10px;padding:3px;border:1px solid #000;margin-bottom:3px">
				<span style="font-size:11px;font-weight:bold">When Returns Are Not Accepted And Warranty Is Voided</span><br>
				<br>
				Example of each are written under subheadings<br>

				<table>
					<tr>
						<td><span style="font-weight:bold">Part Tampering:</span>
							<br>
							1. Changing of any components<br>
							2. Removing of any components<br>
							3. Application of additional components<br><br>
							<span style="font-weight:bold">General:</span><br>
							1. Removal company stickers, stamps and insignias.


						</td>
						<td style="vertical-align:text-top">
							<span style="font-weight:bold">Physical Damanges:</span>
							<br>
							1. Bent Flex Cables<br>
							2. Torn / Ripped Flex Cables<br>
							3. Pressure Marks<br>
							4. Cracked Glass<br>
							5. Cracked LCD<br>



						</td>
					</tr>

				</table>


			</div>


		</td>

	</tr>

</table>

';
		// echo $html;exit;
try {



	$html2pdf = new HTML2PDF('P', 'A4', 'en');

	$html2pdf->setDefaultFont('arial');
	$html2pdf->writeHTML($html);

	$filename = '../files/PackSlip.pdf';
	$file = $html2pdf->Output($filename, 'F');

	// echo $filename.'.pdf';
	
} catch (HTML2PDF_exception $e) {
	echo $e;
	exit;
}



if ($setting['download_pdf'] or $_POST['close_short']==1) {

	$credentials = new PrintNode\Credentials();
	$credentials->setApiKey('19982dc5978951c99f98cdcfe5feb4881cc5147b');
	$request = new PrintNode\Request($credentials);
		// $computers = $request->getComputers();
	$printers = $request->getPrinters();
    	// print_r($printers);exit;
		// $printJobs = $request->getPrintJobs();
	$printJob = new PrintNode\PrintJob();
		// $printJob->printer = 130442; //$printers[1]; /*Dev id*/
		// $printJob->printer = 130444; //$printers[1]; /*Dev id*/
	$printJob->printer = 182844;
	$printJob->contentType = 'pdf_base64';
	$printJob->content = base64_encode(file_get_contents($filename));
	$printJob->source = 'My App/1.0';
	$printJob->title = 'Packing Slip - IMP';
	$response = $request->post($printJob);
	$statusCode = $response->getStatusCode();
	$statusMessage = $response->getStatusMessage();
	$json['success']  = $statusMessage;
	$json['href']  = 0;

}
else
{
	$json['success'] = 1;
	$json['href'] = 1;
}

echo json_encode($json);exit;




}

if(isset($_POST['action'])=='mark_packed' && $_POST['action']=='mark_packed')
{
		// echo 'here';exit;
		// print_r($_POST['sku']);
	$order_id  = $_POST['order_id'];
	$skus = $_POST['sku'];
	$sort_array = array();
	foreach($skus as $sku)
	{
		if(isset($sort_array[$sku]))
		{
			$sort_array[$sku]+=1;
		}
		else
		{
			$sort_array[$sku]=1;

		}
	}
	$json = array();
	if($inventory->markPacked($order_id,$sort_array))
	{
		$json['success'] = 1;
		if($inventory->makeLedger($order_id,$sort_array,$_SESSION['user_id'],'packed'))
		{
			$json['success'] = 1;
		}
		else
		{
			$json['success'] = 0;
		}
	}
	else
	{
		$json['success'] = 0;
	}
	echo json_encode($json);
}


if(isset($_POST['action'])=='mark_adjusted' && $_POST['action']=='mark_adjusted')
{
		// echo 'here';exit;
		// print_r($_POST['sku']);
	$order_id  = $_POST['order_id'];
	$skus = $_POST['sku'];
	$sort_array = array();
	foreach($skus as $sku)
	{
		if(isset($sort_array[$sku]))
		{
			$sort_array[$sku]+=1;
		}
		else
		{
			$sort_array[$sku]=1;

		}
	}
	$json = array();
	if($inventory->markAdjusted($order_id,$sort_array))
	{
		$json['success'] = 1;
		if($inventory->makeLedger($order_id,$sort_array,$_SESSION['user_id'],'adjustment'))
		{
			$inventory->updateInventoryAdjustment($order_id,'adjustment');


			$json['success'] = 1;
		}
		else
		{
			$json['success'] = 0;
		}
	}
	else
	{
		$json['success'] = 0;
	}
	echo json_encode($json);
}

if(isset($_POST['action']) && $_POST['action']=='save_packed')
{
		// print_r($_POST['sku']);exit;
	$order_id  = $_POST['order_id'];
	$skus = $_POST['sku'];
	$sort_array = array();
	foreach($skus as $sku)
	{
		if(isset($sort_array[$sku]))
		{
			$sort_array[$sku]+=1;
		}
		else
		{
			$sort_array[$sku]=1;

		}
	}
		// print_r($sort_array);exit;
	$json = array();
	if($inventory->savePacked($order_id,$sort_array))
	{
		$json['success']=1;

	}
	else
	{
		$json['success'] = 0;
	}
	echo json_encode($json);
}

if(isset($_POST['action'])=='get_rate' && $_POST['action']=='get_rate')
{
		// print_r($_POST['sku']);
	$service_data = explode("~", $_POST['service']);
	$order_id = $_POST['order_id'];
	$order = $inventory->getOrder($order_id);
	$carrier = $service_data[0];
	$service = $service_data[1];
	$package =$_POST['package'];
	$saturday_delivery = $_POST['saturday_delivery'];
	$weight_lb = $_POST['weight_lb'];
	$weight_oz = $_POST['weight_oz'];

	$weight = (float)$weight_lb * 16;
	$weight = (float)$weight + (float)$weight_oz;



	$body = '{
		"shipment": {
			"validate_address": "no_validation",
			"ship_to": {
				"name": "'.$order['shipping_name'].'",
				"phone": "'.$order['telephone'].'",
				"company_name": "'.$order['company'].'",
				"address_line1": "'.$order['address1'].'",
				"city_locality": "'.$order['city'].'",
				"state_province": "'.$order['state_short'].'",
				"postal_code": "'.($order['country_code']=='US'?substr($order['zip'],0,5):$order['zip']).'",
				"country_code": "'.$order['country_code'].'"
			},
			"ship_from": {
				"name": "PhonePartsUSA",
				"phone": "855-213-5588",
				"company_name": "PhonePartsUSA",
				"address_line1": "5145 South Arville Street",
				"address_line2": "Suite A",
				"city_locality": "Las Vegas",
				"state_province": "NV",
				"postal_code": "89118",
				"country_code": "US"
			},';
			if($saturday_delivery=='true')
			{
				$body.='"advanced_options": {
					"saturday_delivery": "true"
				},';
			}

			$body.='"packages": [
			{
				"weight": {
					"value": '.(float)$weight.',
					"unit": "ounce"
				}
			}
			]
		},
		"rate_options": {
			"carrier_ids": [
			"'.$carrier.'"
			]
		}
	}';
// echo $body;exit;
	$response = $inventory->getRates($body);
	$response = json_decode($response,true);
		// echo "<pre>";
		// print_r($response);exit;
	$estimate_rate = 0.00;
	$estimate_delivery = 'N/A';
	foreach($response['rate_response']['rates'] as $rate)
	{
				// if($rate['service_code']=='usps_first_class_mail')
				// {
				// 	echo $service;exit;
				// 	print_r($rate);exit;
				// }
		if($rate['carrier_code']!='stamps_com')
		{
			$package='';
		}

		if(trim($service)!=trim($rate['service_code']) or $rate['package_type']!=$package)
		{

			continue;
		}
		else
		{
			$estimate_rate+=$rate['shipping_amount']['amount'];
			$estimate_rate+=$rate['insurance_amount']['amount'];
			$estimate_rate+=$rate['confirmation_amount']['amount'];
			$estimate_rate+=$rate['other_amount']['amount'];

			$estimate_delivery = date("m/d/Y h:i A", strtotime($rate['estimated_delivery_date']));;
		}		
	}
	echo json_encode(array('rate'=>'$'.number_format($estimate_rate,2),'estimate_delivery_time'=>$estimate_delivery));
}

if(isset($_POST['action'])=='create_label' && $_POST['action']=='create_label')
{
		// print_r($_POST['sku']);
	$service_data = explode("~", $_POST['service']);
	$order_id = $_POST['order_id'];
	$_order_id=explode(",", $order_id);
	$order = $inventory->getOrder($_order_id[0]);
	$carrier = $service_data[0];
	$service = $service_data[1];
	$confimration = $_POST['confimration'];
	$insured_amount = (float)$_POST['insured_amount'];
	$saturday_delivery = $_POST['saturday_delivery'];
	$package = $_POST['package'];

	$weight_lb = $_POST['weight_lb'];
	$weight_oz = $_POST['weight_oz'];

	$weight = (float)$weight_lb * 16;
	$weight = (float)$weight + (float)$weight_oz;


	$body = '{
		"shipment": {
			"service_code": "'.$service.'",
			"ship_to": {
				"name": "'.$order['shipping_name'].'",
				"phone": "'.$order['telephone'].'",';

				if($order['company']!='')
				{
					$body.='"company_name": "'.$order['company'].'",';
				}
				$body.='"address_line1": "'.$order['address1'].'",';

				if($order['address2']!='')
				{
					$body.='"address_line2": "'.$order['address2'].'",';
				}

				$body.='"city_locality": "'.$order['city'].'",
				"state_province": "'.$order['state_short'].'",
				"postal_code": "'.($order['country_code']=='US'?substr($order['zip'],0,5):$order['zip']).'",
				"country_code": "'.$order['country_code'].'",
				"address_residential_indicator": "No"
			},
			"ship_from": {
				"name": "PPUSA",
				"phone": "855-213-5588",
				"address_line1": "5145 South Arville Street",
				"address_line2": "Suite A",
				"city_locality": "Las Vegas",
				"state_province": "NV",
				"postal_code": "89118",
				"country_code": "US",
				"address_residential_indicator": "No"
			},
			"confirmation": "'.$confimration.'",
			';

			if($saturday_delivery=='true')
			{

				$body.='"advanced_options": {
					"saturday_delivery": "true"
				},';
			}

			if($insured_amount>0.00)
			{
				$body.='"insurance_provider": "shipsurance",
				';
			}
			$body.='"customs": {
				"contents": "merchandise",
				"customs_items": [';
				foreach($_order_id as $__order)
				{
					$__order_details = $inventory->getOrder($__order);
					foreach($__order_details['items'] as $order_item)
					{
						$body.='{
							"description": "'.$order_item['name'].'",
							"quantity": '.$order_item['quantity'].',
							"value": '.str_replace(array('$',','), "", $order_item['product_unit']).',

							"country_of_origin": "US"
						},';
					}
				}
				$body.='],
				"non_delivery": "return_to_sender"
			},';
			$body.='"packages": [
			{
				"package_code": "'.$package.'",
				"weight": {
					"value": '.(float)$weight.',
					"unit": "ounce"
				},
				"label_messages": {';
				foreach($_order_id as $__order)
				{
					$__order_details = $inventory->getOrder($__order);
					$i=1;
					foreach($__order_details as $_order_detail)
					{
						$body.='"reference'.$i.'":"'.$_order_detail['order_id'].'",';
						$i++;
					}
				}
					$body.='}';

					if($insured_amount>0.00)
					{
						$body.=',
						"insured_value": {
							"currency": "usd",
							"amount": '.(float)$insured_amount.'
						}';
					}
					$body.='}
					]
				},
				"test_label": false
			}' ;
// echo $body;exit;
			$response = $inventory->getLabel($body);
			$response = json_decode($response,true);
		// echo "<pre>";
		// print_r( $response);exit;
			if($response['errors'])
			{
				$json = array('error'=>$response['errors'][0]['message']);

			}
			else
			{
				$pdf_file =  $response['label_download']['href'];
				$form_file =  $response['form_download']['href'];
				$inventory->markShipped($order_id);
				$inventory->saveLabel($order_id,$response);
				$inventory->updateInventoryShipped($order_id,'shipped');
				$inventory->updateTrackingInfo($order_id,$response['tracking_number'],$response['service_code']);


				$shipping_carrier = explode("_", $response['service_code']);
				$shipping_carrier = $shipping_carrier[0];
				if ($shipping_carrier == 'fedex') {
					$track_url = '<a href="https://www.fedex.com/apps/fedextrack/?action=track&language=english&tracknumbers='.$response['tracking_number'].'" target = "_blank">'.$response['tracking_number'].'</a>';
				}
				if ($shipping_carrier == 'ups') {
					$track_url = '<a href="http://wwwapps.ups.com/WebTracking/processRequest?HTMLVersion=5.0&Requester=NES&AgreeToTermsAndConditions=yes&loc=en_US&tracknum='.$response['tracking_number'].'" target = "_blank">'.$response['tracking_number'].'</a>';
				}
				if ($shipping_carrier == 'usps') {
					$track_url = '<a href="https://tools.usps.com/go/TrackConfirmAction?tLabels='.$response['tracking_number'].'" target = "_blank">'.$response['tracking_number'].'</a>';
				}
				if (!$track_url) 
				{
					$track_url =  $response['tracking_number'];
				}

			// email send

				$emailInfo = array();
				$emailInfo['customer_name'] = $order['shipping_name'];
				$emailInfo['order_id'] = $order_id;
				$emailInfo['email'] = $order['email'];


				$email = array();
				$email['title'] = 'Your order has been shipped!';
				$email['subject'] = $order['shipping_name'].', find your order tracking details!';
				$email['message'] = "Dear ".$order['shipping_name']."<br>Thank you for your order! We wanted to let you know that your order (#".$order_id.") was shipped via ".strtoupper($shipping_carrier).", on ".date('m/d/Y').". You can track your package at any time using the link below.<br><br><strong>Shipped To:</strong><br>".$order['shipping_name']."<br>".$order['address1']."<br>".($order['address2']?$order['address2']."<br>":"").$order['city'].", ".$order['state_short']." ".$order['zip'].' '.$order['country_code']."<br><br><strong>Track Your Shipment:</strong> ".$track_url."<br><br><strong>Thank you for your business and we look forward to serving you in the future!<br><br>PhonePartsUSA Team";

				sendEmailDetails($emailInfo, $email);





				if ($setting['download_label']) {

					$count = 1;
					if($service=='usps' || $service=='fedex')
					{
						$count=2;
					}
					for($j=1;$j<=$count;$j++)
					{
						$credentials = new PrintNode\Credentials();
						$credentials->setApiKey('19982dc5978951c99f98cdcfe5feb4881cc5147b');
						$request = new PrintNode\Request($credentials);

						$printers = $request->getPrinters();

						$printJob = new PrintNode\PrintJob();
						$printJob->printer = 182846;
						$printJob->contentType = 'pdf_base64';
						$printJob->content = base64_encode(file_get_contents($pdf_file));
						$printJob->source = 'My App/1.0';
						$printJob->title = 'Label Print - IMP';
						$response = $request->post($printJob);
						$statusCode = $response->getStatusCode();
						$statusMessage = $response->getStatusMessage();

						if($form_file)
						{
							$printJob = new PrintNode\PrintJob();
							$printJob->printer = 182846;
							$printJob->contentType = 'pdf_base64';
							$printJob->content = base64_encode(file_get_contents($form_file));
							$printJob->source = 'My App/1.0';
							$printJob->title = 'Custom Form Print - IMP';
							$response = $request->post($printJob);
							$statusCode = $response->getStatusCode();
							$statusMessage = $response->getStatusMessage();


						}
					}


					$json['success'] = 1;
					$json['href']  = 0;

				}
				else
				{
					$json['success'] = $pdf_file;
					if($form_file)
					{
						$json['success2'] = $form_file;
					}
					$json['href'] = 1;
				}


			}

			echo json_encode($json);
		}
		if(isset($_POST['action'])=='get_packages' && $_POST['action']=='get_packages')
		{
		// print_r($_POST['sku']);
			$service_data = explode("~", $_POST['service']);

			$carrier = $service_data[0];
			$service = $service_data[1];

			$response = $inventory->getPackages($carrier);

			echo $response;exit;

		}

		if(isset($_POST['action'])=='get_carrier_options' && $_POST['action']=='get_carrier_options')
		{
		// print_r($_POST['sku']);
			$service_data = explode("~", $_POST['service']);

			$carrier = $service_data[0];
			$service = $service_data[1];

			$response = $inventory->getCarrierOptions($carrier);

			$response = json_decode($response,true);
			$json = array();
			$json['error'] = 'Carrier '.$carrier.' not found';

			if(strpos($service,'overnight')!==false){




				if(isset($response['options']))
				{
					foreach($response['options'] as $option)
					{
						if($option['name']=='saturday_delivery')
						{
							unset($json);
							$json['success']  = 1;
							break;
						}
					}
				}
			}


			echo json_encode($json);exit;
		}


		if(isset($_POST['action']) && $_POST['action']=='void_label')
		{


			$order_id = $_POST['order_id'];

			$label_data = $db->func_query_first("SELECT * FROM inv_label_data WHERE order_id='".$order_id."' and voided=0 order by id desc limit 1");	
			$label_id = $label_data['label_id'];
			if($label_data['combined_orders'])
			{
				$order_ids = $label_data['combined_orders'];
			}	
			else
			{
				$order_ids = $order_id;
			}
		// echo $label_id.'a';exit;


			$res = $inventory->voidLabel($label_id);
			$res = json_decode($res,true);

		// echo $res.'aaa';exit;




			$json = array();

			if($res['approved']==true){


				$json['success'] = $res['message'];
				if(!isset($_POST['from_order']))
				{
					foreach(explode(",",$order_ids) as $order_id)
					{
					$inventory->markProcessed($order_id);
					$inventory->updateInventoryCancel($order_id,'rollback');
					}
				}

			}
			else
			{
				$json['error'] = 1;
			}
			echo json_encode(($json));

		}


		if(isset($_POST['action']) && $_POST['action']=='reprint_label')
		{
			$order_id = $_POST['order_id'];
			$href = $db->func_query_first_cell("SELECT label_download FROM inv_label_data WHERE order_id='".$order_id."' and voided=0 order by id desc limit 1");	
		// echo $label_id.'a';exit;

		// print_r($response);exit;
			$json = array();
			if($href){
				$json['success'] = $href;

			}
			else
			{
				$json['error'] = 1;
			}
			echo json_encode(($json));

		}

		if(isset($_POST['action']) && $_POST['action']=='verify_manager_pin')
		{

			$pin = $db->func_escape_string($_POST['pin_no']);

			$id = $db->func_query_first_cell("SELECT id FROM inv_users WHERE is_manager = '1' AND manager_pin = md5(concat(email,'". $pin ."',salt))");	
			$json = array();
			if($id)
			{
				$json['success'] = 1;
			}
			else
			{
				$json['error'] = 1;
			}
			echo json_encode(($json));

		}

		if(isset($_POST['action']) && $_POST['action']=='local_order_view')
		{
			if(!isset($_SESSION['inv_load_local_order']))
			{
				$_SESSION['inv_load_local_order'] = 1;
			}
			else
			{
				unset($_SESSION['inv_load_local_order']);
			}
			echo json_encode(array('success'=>1));
		}

		if(isset($_POST['action']) && $_POST['action']=='create_manifest')
		{

			$date  = $_POST['date'];
			$carrier_id = $_POST['carrier_id'];
			$new_date = date("Y-m-d\TH:i:s.000\Z", strtotime("$date 23:59:59"));

			$body = '{
				"carrier_id": "'.$carrier_id.'",
				"excluded_label_ids": [],
				"warehouse_id": 336237,
				"ship_date": "'.$new_date.'"
			}';
	// echo $body;exit;
		// $body = $inventory->buildManifestBody();
			$manifest = $inventory->createManifest($body);
			$manifest = json_decode($manifest,true);

			$json = array();
			if($manifest['errors'])
			{
				$json['error'] = $manifest['errors'][0]['message'];
			}
			else
			{
				$json['success']  = $manifest['manifest_download']['href'];
			}
			echo json_encode($json);
		}

		if(isset($_POST['action']) && $_POST['action']=='combine_orders')
		{
			$order_id = $_POST['order_id'];
			$main_order = $inventory->getOrder($order_id);

			$combine_orders = $_POST['combine_orders'];
			$combine_orders = explode(",", $combine_orders);
			$json = array();
			$error  = false;
			$data = array();
			$items = array();

			foreach($combine_orders as $combine_order)
			{
				$check = $inventory->getOrder($combine_order);
				$data[] = trim($combine_order);
				$items[] = $check['items'];
				if(trim(strtolower($check['email']))!=trim(strtolower($main_order['email'])) and strtolower($check['order_status'])!='processed' and $check['order_id']!=$main_order['order_id'] )
				{

					$error = true;
					break;
				}
			}
			if($error)
			{
				$json['error'] = 1;
			}
			else
			{
				$json['success'] = implode(",", $data);
				$json['items'] = $items;
			}

			echo json_encode($json);
		}

		if(isset($_POST['action']) && $_POST['action']=='print_eod_totals')
		{
			$date = $_POST['date'];
			$headers = $inventory->getLabelHeaders();
		// echo $date;exit;

		// echo date('l F d Y',strtotime($date));exit;

			$html='<page><page_footer>

			<table class="page_footer" align="right">
				<tr>
					<td align="right" style="width: 100%; text-align: right">
						Page [[page_cu]] of [[page_nb]]
					</td>
				</tr>
			</table>
			</page_footer></page>';
			$html.='
			<table  border="0">
				<tr>
					<td style="width:500px"><h2>End of Day Shipment Totals</h2></td>

					<td align="right">
						'.date('l F d Y',strtotime($date)).'
					</td>
				</tr>

			</table>';

			$headers[] = array('name'=>'local orders');
			foreach($headers as $header)
			{

				$html.='<table border="0" style="width:100%;" cellpadding="0" cellspacing="0" >
				<tr style="font-weight:bold; ">
					<td style="width:300px;height:20px;padding:5px;border-top:2px solid black;border-bottom:2px solid black;">'.$inventory->modifyString($header['name']).'</td>
					<td style="width:110px;padding:5px;border-top:2px solid black;border-bottom:2px solid black;">Domestic</td>
					<td style="width:110px;padding:5px;border-top:2px solid black;border-bottom:2px solid black;">Intl</td>
					<td style="width:110px;padding:5px;border-top:2px solid black;border-bottom:2px solid black;">Total</td>

				</tr>';
				$filter = array('date'=>$date,'carrier'=>$header['name']);
				$items = $inventory->getLabelData($filter);
				$total_international = 0;
				$total_domestic = 0;
				foreach($items as $item)
				{
					$service_name = $inventory->modifyString($item['service_code']);

					if (strpos($item['service_code'], 'international') !== false) {
						$international_qty  = $item['qty'];
						$domestic_qty = 0;
					}
					else
					{
						$domestic_qty  = $item['qty'];
						$international_qty = 0;
					}

					$html.='<tr style="font-size:12px;border-top:1px solid #000000">
					<td style="padding:5px;vertical-align:text-top;b;border-bottom:2px solid black;">'.$service_name.'</td>
					<td style="padding:5px; vertical-align:text-top;b;border-bottom:2px solid black;">'.(int)$domestic_qty.'</td>
					<td style="padding:5px;word-wrap: break-word;vertical-align:text-top;b;border-bottom:2px solid black;">'.(int)$international_qty.'</td>
					<td style="padding:5px;vertical-align:text-top;text-align:center;border-bottom:2px solid black;">'.($domestic_qty+$international_qty).'</td>

				</tr>';

				$total_international+=$international_qty;
				$total_domestic+=$domestic_qty;

			}

			$html.='<tr style="font-size:12px;border-top:1px solid #000000">
			<td style="padding:5px;vertical-align:text-top;b;border-bottom:2px solid black;"></td>
			<td style="padding:5px; vertical-align:text-top;b;border-bottom:2px solid black;">'.(int)$total_domestic.'</td>
			<td style="padding:5px;word-wrap: break-word;width:100px;vertical-align:text-top;b;border-bottom:2px solid black;">'.(int)$total_international.'</td>
			<td style="padding:5px;vertical-align:text-top;text-align:center;border-bottom:2px solid black;">'.($total_domestic+$total_international).'</td>

		</tr>';





		$html.='</table><br>';
	}

		// echo $html;exit;
	$json = array();
	try {



		$html2pdf = new HTML2PDF('P', 'A4', 'en');

		$html2pdf->setDefaultFont('arial');
		$html2pdf->writeHTML($html);

		$filename = '../files/EOD_Totals_'.date('m-d-Y').'_'.time().'.pdf';
		$file = $html2pdf->Output($filename, 'F');

	// echo $filename.'.pdf';

	} catch (HTML2PDF_exception $e) {
		$json['error'] =  $e;
  	// exit;
	}


	if(!$json['error'])
	{

		if ($setting['download_report']) {
  // $credentials = 'f9305047bdf9a187cfc02de4780b8e0c7cb3261a'; /*Dev ID*/
			$credentials = new PrintNode\Credentials();
			$credentials->setApiKey('19982dc5978951c99f98cdcfe5feb4881cc5147b');
			$request = new PrintNode\Request($credentials);
		// $computers = $request->getComputers();
			$printers = $request->getPrinters();
    	// print_r($printers);exit;
		// $printJobs = $request->getPrintJobs();
			$printJob = new PrintNode\PrintJob();
		// $printJob->printer = 130442; //$printers[1]; /*Dev id*/
		// $printJob->printer = 130444; //$printers[1]; /*Dev id*/
			$printJob->printer = 182846;
			$printJob->contentType = 'pdf_base64';
			$printJob->content = base64_encode(file_get_contents($filename));
			$printJob->source = 'My App/1.0';
			$printJob->title = 'EOD Totals';
			$response = $request->post($printJob);
			$statusCode = $response->getStatusCode();
			$statusMessage = $response->getStatusMessage();


			$json['success']  = $statusMessage;
			$json['href']  = 0;

		}
		else
		{
			$json['success'] = $filename;
			$json['href'] = 1;
		}

	}

	echo json_encode($json);exit;




}

if(isset($_POST['action']) && $_POST['action']=='close_short_popup')
	{
		$skus = $_POST['skus'];

		$products = array();

		foreach($skus as $sku)
		{
			$_temp = explode("~", $sku);
			$products[] = $_temp[1];
		}

		$json = array();
		if($products)
		{
			$json['success']=1;
			$body='';
			$body.='<table width="50%">';
			$body.='<tr>';
			$body.='<th>SKU</th>';
			// $body.='<th>On Hand</th>';
			$body.='<th>On Shelf</th>';
			$body.='</tr>';
			foreach(array_unique($products) as $product)
			{
				$inv_data = $inventory->getInventoryDetail($product);
				$body.='<tr>';
				$body.='<td>'.$product.'</td>';
				// $body.='<td>'.$inv_data['on_hand'].'</td>';
				$body.='<td>'.$inv_data['on_shelf'].'</td>';
				$body.='</tr>';
			}
			$body.='</table><br><h4>Are you sure you want to remove unscanned quantity from the order and set shelf count to 0?</h4>';
			$json['body'] = $body;
		}
		else
		{
			$json['error'] = 1;
		}

		echo json_encode($json);exit;

	}

	if(isset($_POST['action']) && $_POST['action']=='discard_removals')
	{

		$order_id=$_POST['order_id'];

		$order_detail = $inventory->getOrder($order_id,true);
		print_r($order_detail);exit;
		$skus = $order_detail['items'];
	$sort_array = array();
	foreach($skus as $sku)
	{
		if(isset($sort_array[$sku['sku']]))
		{
			$sort_array[$sku['sku']]+=$sku['quantity'];
		}
		else
		{
			$sort_array[$sku['sku']]=$sku['quantity'];

		}
	}
	print_r($sort_array);exit;


		$json = array();

	if($inventory->markAdjusted($order_id,$sort_array) && $_SESSION['login_as']=='admin')
	{
		$json['success'] = 1;
		if($inventory->makeLedger($order_id,$sort_array,$_SESSION['user_id'],'discard_removals'))
		{
			$json['success'] = 1;
		}
		else
		{
			$json['success'] = 0;
		}
	}
	else
	{
		$json['success'] = 0;
	}
	echo json_encode($json);
	}


exit;
}

?>