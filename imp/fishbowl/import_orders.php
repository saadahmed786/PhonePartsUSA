<?php

require_once("applicationTop.php");

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");

if(isset($_POST['Import'])){
	$order_ids = explode(",",$_POST['order_ids']);
	foreach($order_ids as $order_id){
		$order_url = $host_path . "/fishbowl/fetch_order.php?order_id=$order_id";
		
		$ch = curl_init();
		curl_setopt($ch , CURLOPT_URL , $order_url);
		curl_setopt($ch , CURLOPT_TIMEOUT, 10);
		curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
		$order = curl_exec($ch);
		$order = json_decode($order , true);
		if($order){			
			// Get sales order list
			$first_name  = $fbapi->replaceSpecial($order['first_name']);
			$last_name   = $fbapi->replaceSpecial($order['last_name']);
			$name = $first_name." ".$last_name;
			
			$getCustomer = $fbapi->getCustomer("Get",$name);
			$customer    = @$getCustomer['FbiMsgsRs']['CustomerGetRs']['Customer'];
			
			//print_r($cusotmer); exit;
			if(!$customer){
				$customer_result = $fbapi->addCustomer($order);
				
				$attributes = $customer_result['FbiMsgsRs'][0]->attributes();
				//print_r($attributes); exit;
				
				if($attributes['statusCode'] == 3001){
					$first_name = $first_name."1";
					$name = $first_name." ".$last_name;
					
					$order['first_name'] = $first_name;
					$customer_result = $fbapi->addCustomer($order);
				}
			}
			else{
				//customer exist
			}
			
			//print_r($getCustomer); exit;
			// add order
			$order_result = $fbapi->saveSOOrder($order);
			$result = $order_result['result'];
		
			$FbiMsgsRsStatus = $result['FbiMsgsRs']['@attributes']['statusCode'];
			$SaveSORsStatus  = $result['FbiMsgsRs']['SaveSORs']['@attributes']['statusCode'];
			
			$SaveSORsMessage = false;
			if(!$SaveSORsStatus and $result['FbiMsgsRs'][0]){
				$attributes = $result['FbiMsgsRs'][0]->attributes();
				$SaveSORsStatus  = $attributes['statusCode'];
				$SaveSORsMessage = $attributes['statusMessage'];
			}
			
			print $FbiMsgsRsStatus . " -- " . $SaveSORsStatus . '--'. $SaveSORsMessage . " -- ".$order['order_id']. "--". $order['store_type']. "<br />";
			
			if ($FbiMsgsRsStatus == 1000 &&  $SaveSORsStatus == 1000) {
				$successIds[] = "'" . $order['order_id'] . "'";
				if($result['FbiMsgsRs']['SaveSORs']['SalesOrder']['Number']){
					$OrderSoNumbers[$order['order_id']] = array(
		                    'SoNumber' => $result['FbiMsgsRs']['SaveSORs']['SalesOrder']['Number'],
		                    'Items'    => $order_result['items']
					);
				}
			}
			elseif($FbiMsgsRsStatus == 1000 &&  $SaveSORsStatus == 2100){
				//model not exist in fishbowl software
				$nomappingIds[] = "'" . $order['order_id'] . "'";
				$errorMessage[$order['order_id']] = array($SaveSORsStatus,$SaveSORsMessage,$FbiMsgsRsStatus,$order_result,$customer_result);
			}
			else{
				$nomappingIds[] = "'" . $order['order_id'] . "'";
				$errorMessage[$order['order_id']] = array($SaveSORsStatus,$SaveSORsMessage,$FbiMsgsRsStatus,$order_result,$customer_result);
			}
		}
		else{
			$_SESSION['message'][] = "Order ID - $order_id not found";
		}
	}
	
	if(($successIds and count($successIds) > 0) || ($nomappingIds and count($nomappingIds) > 0)){
		$successIdsStr = implode(",",$successIds);
		$nomappingIdsStr = implode(",",$nomappingIds);
	
		$updateUrl = $host_path . "/fishbowl/updateOrders.php";
		$ch = curl_init();
		curl_setopt($ch , CURLOPT_URL , $updateUrl);
		curl_setopt($ch , CURLOPT_POST , 1);
		curl_setopt($ch , CURLOPT_POSTFIELDS , array('successIdsStr' => $successIdsStr ,
	                                                 'OrderSoNumbers' => json_encode($OrderSoNumbers) , 
	                                                 'nomappingIdsStr' => $nomappingIdsStr,
	                                                 'errorMessage' => json_encode($errorMessage)
		));
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		curl_exec($ch);
	}
}

if($_SESSION['message']){
	$_SESSION['message'] = implode("<br />",$_SESSION['message']);	
}
?>
<html>
  <head>
     <script src="js/jquery.min.js"></script>
  </head>
  <body>
  	 <div>
  	 	 <div id="message" align="center">
	  	 	 <?php if(isset($_SESSION['message'])):?>
	  	 	 		<p style="color:red;"><?php echo $_SESSION['message']; unset($_SESSION['message']);?></p>
	  	 	 <?php endif;?>
	  	 </div>	 
	  	 
  	 	 <center>
  	 	 	<h2>Manual import Orders to FB</h2>
  	 	 	<form method="post">
  	 	 		<table>
  	 	 			<tr>
  	 	 				<td align="center">OrderIDs</td>
  	 	 			</tr>
  	 	 			
  	 	 			<tr>
  	 	 				<td>
  	 	 					<textarea rows="3" cols="50" name="order_ids" required style="resize:none"></textarea>
  	 	 					<br />
  	 	 					<b>Please enter order ids in comma seperated</b>
  	 	 					<br /><br />
  	 	 				</td>
  	 	 			</tr>
  	 	 			
  	 	 			<tr>
  	 	 				<td align="center"><input type="submit" name="Import" value="Import" /></td>
  	 	 			</tr>
  	 	 		</table>
  	 	 	</form>
  	 	 </center>
     </div>
  </body>
</html>       	 	 