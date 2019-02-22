<?php
/**
* Updating New Class
*/
include_once '../config.php';
include_once '../inc/functions.php';

class customerAPI extends Database {

	private $__db;
	public function getCustomerData($email)
	{
		global $host_path;
		$row = $this->func_query_first("SELECT * FROM inv_customers WHERE email='$email'");

		$return = array();
		if($row and $email!='')
		{
			$lastOrderID = $this->func_query_first_cell("SELECT order_id from inv_orders WHERE email='".$email."' ORDER BY order_date DESC LIMIT 1");
			$lastTransactionID = $this->func_query_first_cell("SELECT tracking_number FROM inv_shipstation_transactions WHERE order_id='$lastOrderID' ");
			$return = 
					array(
							"lastOrderID" => linkToOrder($lastOrderID,$host_path,'target="_blank"'),
							"totalOrderAmount" => '$'.number_format((float)$row["total_amount"],2),
							"noOfOrders"=> $row["no_of_orders"],
							"customerGroup"=>$row["customer_group"],
							"lastTransactionID"=>$lastTransactionID
						);
		}
		$return = $_GET['callback']."([".json_encode($return)."])";
		return $return;
	}

}

$email = $_GET['requestor_email'];
$email = strtolower($email);
$email = trim($email);
header('Content-type: application/x-javascript');
// echo $_GET['callback']."([".json_encode($_GET)."])";exit;
//header('HTTP 1.0 200 OK');
// echo json_encode(array('customerGroup'=>'hello world'));exit;
//$email='Rapidfiregaming1@gmail.com';
$customer_data = new customerAPI();
echo $customer_data->getCustomerData($email);
?>