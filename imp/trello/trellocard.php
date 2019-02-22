<?php
/**
* Updating New Class
*/
class trello extends Database {

	private $__db,
		// Testing API
	// $__T_APIKEY = '2cd7c8ac0e4b4a0ccf9d9f2c4ba79e09',
	// $__T_AUTHTOKEN = '055c02189d56a7081e892e865064db44b0a77cd0cca3c1abc16c9f8c116576b0',
	// $__T_LIST = '561c755d1eb3a587d8a5efc3',
	// $__T_MEMBERS = '55ba0d571d8399f71437394e,55ba0e29f7096c9f1c14474e,5338fe47e768de1a2bf4e38f',

	//Live
	$__T_APIKEY = 'c3db657c3ea576938bdd770760da2dba',
	$__T_AUTHTOKEN = '742e4b539010d79d90d99b59be780b954d5d84c2f506404eef3ff2b820a4c96a',
	$__T_LIST = '5612ff07e9ed2abfdb703895',
	$__T_MEMBERS = '5616b3eadf32bddf1966faaa,561bf5ad7cb5e886be0272a9,560eb3d74896cec0d07daa7d',
	$__T_T_TEMP = array(
		'title' => '{{user_name}} has {{type}} Order #: {{order_id}}',
		'updated' => 'SKU: `{{xsku}}`, Quantity `{{xqty}}` has been updated to SKU: `{{sku}}`, Quantity: `{{qty}}` ({{url}})',
		'canceled the' => '({{url}})',
		'removed an item from' => 'SKU: `{{xsku}}`, Quantity `{{xqty}}` ({{url}})',
		'removed Items from' => 'SKU: `{{products}}`, ({{url}})',
		'added an item to' => 'SKU: `{{sku}}`, Quantity `{{qty}}` ({{url}})',
		'put Hold On' => 'Customer Name: `{{customer_name}}`, Telephone `{{customer_telephone}}`, Shipping Address {{shipping_address}}, Billing Address: {{billing_address}}, Transaction ID: {{transaction_id}}, AVS Data: {{avs_data}}',
		);

	public function addCard($order_id, $name, $desc, $labels, $idMembers = array(),$trello_list_id=''){
		if ($idMembers) {
			$idMembers = implode(',', $idMembers);
		} else {
			$idMembers = $this->__T_MEMBERS;
		}
		if($trello_list_id=='')
		{
			$list_id = $this->__T_LIST;
		}
		else
		{
			$list_id = $trello_list_id;
		}


		$url = 'https://api.trello.com/1/lists/'. $list_id .'/cards?key='. $this->__T_APIKEY . '&token=' . $this->__T_AUTHTOKEN;

		$data = array(
			'name=' . $name,
			'desc=' . $desc,
			'labels=' . $labels,
			'idMembers=' . $idMembers,
			'due=' . ''
			);

		$data_string = implode('&', $data);
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

		$output = curl_exec($ch);

		curl_close($ch);

		$result = json_decode($output, true);
		file_put_contents('logs.txt', $result, FILE_APPEND);
		$addcomment = array();
		$addcomment['date_added'] = date('Y-m-d H:i:s');
		$addcomment['user_id'] = $_SESSION['user_id'];
		$addcomment['comment'] = 'Record Updated to Trello <a traget="_blank" href="' . $result['shortUrl'] . '">click here</a> to check';
		$addcomment['order_id'] = $order_id;
		$order_history_id = $this->func_array2insert("inv_order_history", $addcomment);
	}
	public function makeTemplate($order)	{
		$name = $this->shortCodeReplace($order, $this->__T_T_TEMP['title']);
		$desc = $this->shortCodeReplace($order, $this->__T_T_TEMP[$order['type']]);
		return array('name' => $name, 'desc' => $desc, 'order_id' => $order['order_id']);
	}

	private function shortCodeReplace ($data, $message) {
		foreach ($data as $key => $value) {
			$message = str_replace('{{'.$key.'}}', $value, $message);
		}
		return $message;
	}

	public function newSKUImages($sku_data,$shipment_number='',$date_added='')
	{
		// $list_id = '560eb4fc4f035c51ea39b69b';
		$list_id = '561c755d1eb3a587d8a5efc3';
		// $idMembers = '561423941d19acf6a1688a23,561bf5ad7cb5e886be0272a9';
		$idMembers = '55ba0d571d8399f71437394e,55ba0e29f7096c9f1c14474e';
		
		$url = 'https://api.trello.com/1/lists/'. $list_id .'/cards?key='. $this->__T_APIKEY . '&token=' . $this->__T_AUTHTOKEN;
		$data = array(
			'name='  . date('F j', strtotime($date_received)) . ' ' . $shipment_number,
			'desc=' . '',
			'labels=' . '',
			'idMembers=' . $idMembers,
			'due=' . ''
			);

		$data_string = implode('&', $data);
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

		$output = curl_exec($ch);
// $reponseInfo = curl_getinfo($output);
		curl_close($ch);
			
		$result = json_decode($output, true);
			// echo $output;exit;
		// file_put_contents('logs_image.txt', $output, FILE_APPEND);		
			$url = 'https://api.trello.com/1/cards/'. $result['id'] .'/checklists?key='. $this->__T_APIKEY . '&token=' . $this->__T_AUTHTOKEN;
			
				$data = array('name=Shipment # '.$shipment_number);
				$data_string = implode('&', $data);
		
				$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, $url);

				curl_setopt($ch,CURLOPT_POST, 1);
				curl_setopt($ch,CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

				$output = curl_exec($ch);
				curl_close($ch);
				
				$result2 = json_decode($output, true);

				$url = 'https://api.trello.com/1/cards/'. $result['id'] .'/checklist/'.$result2['id'].'/checkItem?key='. $this->__T_APIKEY . '&token=' . $this->__T_AUTHTOKEN;	

			foreach($sku_data as $sku)
			{
				$data = array(
						'idChecklist='.$result2['id'],
						'name='.$sku['sku'] . ' - '.$sku['product_name']
					);

				$data_string = implode('&', $data);
		
				$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, $url);

				curl_setopt($ch,CURLOPT_POST, 1);
				curl_setopt($ch,CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

				$output = curl_exec($ch);
				curl_close($ch);

			}
				
	}

}
?>