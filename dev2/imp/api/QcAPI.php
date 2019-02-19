<?php

class QcAPI{

	private $api_key = '';

	public $request;

	public $db;

	public $requestOs = false;

	public $requestTime = false;

	public function QcAPI(){
		global $db;

		$this->db = $db;
		$this->request = $_POST;
	}

	public function index(){
		if(!$this->request){
			$error = array("Ack"=>"Error","Error"=>array("Code"=>403,"Message"=>"Get method request is not allowed. Please check api documentations."));
			$responseJson = json_encode($error);
			echo $responseJson;
			exit;
		}
		else{
			$methodName = $this->request['MethodName'];

			if(!method_exists($this,$methodName)){
				$error = array("Ack"=>"Error","Error"=>array("Code"=>404,"Message"=>"Requested method is not found. Please check api documentations."));
				$responseJson = json_encode($error);
				echo $responseJson;
				exit;
			}
				
			$requestJson = $this->request['Json'];
			$requestJsonObject = json_decode($requestJson);
			if(!$requestJsonObject){
				$error = array("Ack"=>"Error","Error"=>array("Code"=>500,"Message"=>"Bad request. Please check api documentations."));
				$responseJson = json_encode($error);
				echo $responseJson;
				exit;
			}
			else{
				try{
					$this->requestOs   = $requestJsonObject->RequestOs;
					$this->requestTime = $requestJsonObject->RequestTime;

					$response = self::$methodName($requestJsonObject);
					$response = array("Ack"=>"Success","Result"=>$response);
					$responseJson = json_encode($response);
						
					mail("vipin.garg12@gmail.com","PPUSA",print_r($this->request['Json'],true) . print_r($_FILES,true) . print_r($responseJson,true));
					echo $responseJson;
					exit;
				}
				catch(Exception $e){
					$response = array("Ack"=>"Error","Result"=>array(),"Error"=>array("Message"=>$e->getMessage(),"Code"=>$e->getCode()));
					$responseJson = json_encode($response);

					echo $responseJson;
					exit;
				}
			}
		}
	}

	private function login($requestJsonObject){
		$requestInput = $requestJsonObject->RequestInput;

		$email = $requestInput->email;
		$password = $requestInput->password;

		$isExist = $this->db->func_query("select * from inv_users where email = '$email' and password = '$password'");
		if(!$isExist){
			throw new Exception("Invalid username or password", 1001);
		}
		else{
			return $isExist;
		}
	}

	private function register($requestJsonObject){
		$requestInput = $requestJsonObject->RequestInput;

		$name  = $requestInput->name;
		$email = $requestInput->email;
		$password = $requestInput->password;

		$isExist = $this->db->func_query_first("select id from inv_users where email = '$email'");
		if($isExist){
			throw new Exception("A user is already exist with this email $email", 1001);
		}
		else{
			$user = array();
			$user['name']  = $name;
			$user['email'] = $email;
			$user['password']  = $password;
			$user['status']    = 1;
			$user['group_id']  = 3; // QC
			$user['dateofmodification']  = date('Y-m-d H:i:s');

			$user_id = $this->db->func_array2insert("inv_users", $user);
			return $user_id;
		}
	}

	private function getShipments($requestJsonObject){
		$requestInput = $requestJsonObject->RequestInput;

		$page  = (int)@$requestInput->page;
		if(!$page){
			$page = 1;
		}

		$limit = (int)@$requestInput->limit;
		if(!$limit){
			$limit = 100;
		}

		$start = ($page-1)*$limit; //where status != 'Completed'
		$shipments = $this->db->func_query("select id , package_number , (select count(id) from inv_rejected_shipment_items where rejected_shipment_id = r.id and qc_app_uploaded = 0) as total from inv_rejected_shipments r having total > 0 order by date_added DESC limit $start , $limit");

		$totalCount = $this->db->func_query_first_cell("select count(*) from inv_rejected_shipments");

		$pagination = array('page' => $page , 'limit' => $limit , 'totalCount' => $totalCount);
		return array("Pagination" => $pagination, "shipments" => $shipments);
	}

	private function getShipmentItems($requestJsonObject){
		$requestInput = $requestJsonObject->RequestInput;

		$shipment_id = (int)$requestInput->shipment_id;
		$shipment_detail = $this->db->func_query_first("select * from inv_rejected_shipments where id = '$shipment_id'");

		if(!$shipment_detail){
			throw new Exception("This shipment id is not valid", 1001);
		}

		$inv_query  = "select si.product_sku , si.qty_rejected , s.package_number, si.shipment_id from inv_rejected_shipment_items si inner join inv_shipments s on (si.shipment_id = s.id) where rejected_shipment_id = '$shipment_id' order by shipment_id";
		$shipment_items = $this->db->func_query($inv_query);

		$count = 1;
		$shipment_id = $shipment_items[0]['shipment_id'];
		$shipment_reject_items = array();

		$reject_ids = $this->db->func_query("select item_id from inv_product_issues pi left join inv_product_issue_images pii on (pi.id = pii.product_issue_id) where shipment_id = '$shipment_id' and issue_from = 'shipment'","item_id");
		$reject_ids = array_keys($reject_ids);

		$index = 0;
		foreach($shipment_items as $shipment_item){
			if($shipment_id != $shipment_item['shipment_id']){
				$count = 1;
				$shipment_id = $shipment_item['shipment_id'];
			}

			for($j=0;$j<$shipment_item['qty_rejected'];$j++){
				$shipment_reject_items[$index] = $shipment_item;

				$reject_id = $shipment_item['package_number'] . "_". $count;
				if(!in_array($reject_id, $reject_ids)){
					$shipment_reject_items[$index]['reject_id'] = $reject_id;
					$index++;
				}

				$count++;
			}
		}

		return array("ShipmentItems"=> $shipment_reject_items);
	}

	private function getReturns($requestJsonObject){
		$requestInput = $requestJsonObject->RequestInput;

		$page  = (int)@$requestInput->page;
		if(!$page){
			$page = 1;
		}

		$limit = (int)@$requestInput->limit;
		if(!$limit){
			$limit = 100;
		}

		$start = ($page-1)*$limit;
		$shipments = $this->db->func_query("select id , rma_number , (select count(id) from inv_return_items where return_id = r.id and qc_app_uploaded = 0) as total from inv_returns r where rma_status = 'Received' having total > 0 order by date_added DESC limit $start , $limit");

		$totalCount = $this->db->func_query_first_cell("select count(*) from inv_returns");

		$pagination = array('page' => $page , 'limit' => $limit , 'totalCount' => $totalCount);
		return array("Pagination" => $pagination, "returns" => $shipments);
	}

	private function getReturnItems($requestJsonObject){
		$requestInput = $requestJsonObject->RequestInput;

		$return_id = (int)$requestInput->return_id;
		$return_detail = $this->db->func_query_first("select * from inv_returns where id = '$return_id'");

		if(!$return_detail){
			throw new Exception("This return id is not valid", 1001);
		}

		$_query = "select id , sku , item_condition , item_issue from inv_return_items where return_id = '$return_id' and
				   id not in(select item_id from inv_product_issues pi left join inv_product_issue_images pii on (pi.id = pii.product_issue_id) and issue_from = 'returns')";

		$return_items = $this->db->func_query($_query);
		return array("ReturnItems"=> $return_items);
	}

	private function getReturnDetail($requestJsonObject){
		$requestInput = $requestJsonObject->RequestInput;

		$return_id = (int)$requestInput->return_id;
		$return_detail = $this->db->func_query_first("select * from inv_returns where id = '$return_id'");

		if(!$return_detail){
			throw new Exception("This return id is not valid", 1001);
		}

		return $return_detail;
	}

	private function getShipmentDetail($requestJsonObject){
		$requestInput = $requestJsonObject->RequestInput;

		$shipment_id = (int)$requestInput->shipment_id;
		$shipment_detail = $this->db->func_query_first("select * from inv_rejected_shipments where id = '$shipment_id'");

		if(!$shipment_detail){
			throw new Exception("This shipment id is not valid", 1001);
		}

		return $shipment_detail;
	}

	private function addItemIssue($requestJsonObject){
		$requestInput = $requestJsonObject->RequestInput;

		$product_sku = $requestInput->product_sku;
		if(!$product_sku){
			throw new Exception("Product SKU is required", 1001);
		}

		$issue_from = $requestInput->issue_from;
		if(!$issue_from OR !in_array($issue_from, array("shipment","returns"))){
			throw new Exception("Issue from is invalid or missing. It should be  shipment or returns.", 1001);
		}

		if(!$_FILES['photos']){
			//throw new Exception("Issue images are missing.", 1002);
		}

		$item_condition = $requestInput->item_condition;
		$item_issue = $requestInput->item_issue;

		$productIssue = array();
		$productIssue['issue_from']    = $issue_from;
		$productIssue['product_sku']   = $product_sku;
		$productIssue['item_issue']    = $requestInput->item_issue;
		$productIssue['shipment_id']   = $requestInput->shipment_id;
		$productIssue['item_id']       = $requestInput->item_id;
		$productIssue['username']      = $requestInput->username;
		$productIssue['last_issue_date']   = date('Y-m-d H:i:s');
		$productIssue['date_added']   = date('Y-m-d H:i:s');

		$product_issue_id = $this->db->func_array2insert("inv_product_issues", $productIssue);

		//insert images
		for($i=0; $i<count($_FILES['photos']['tmp_name']); $i++){
			if($_FILES['photos']['tmp_name'][$i]){
				$uniqid = uniqid();

				$filename = $uniqid.".jpg";
				$filename_thumb = $uniqid."_thumb.jpg";

				$destination = "../images/issues/$filename";
				$destination_thumb = "../images/issues/$filename_thumb";

				if(move_uploaded_file($_FILES['photos']['tmp_name'][$i], $destination))
				{
					$productIssueImage = array();
					$productIssueImage['product_issue_id'] = $product_issue_id;
					$productIssueImage['image_path'] = "issues/$filename";

					$this->resizeImage($destination, $destination, 500, 500);
					$this->resizeImage($destination, $destination_thumb, 50, 50);

					if(strlen($productIssue['item_issue']) > 3){
						$this->db->func_array2insert("inv_product_issue_images", $productIssueImage);
					}
					else{
						$user = $this->db->func_query_first("select id from inv_users where name = '".$requestInput->username."'");
						$user_id = $user['id'];

						$itemImage = array();
						$itemImage['image_path'] = $destination;
						$itemImage['thumb_path'] = $destination_thumb;
						$itemImage['date_added'] = date('Y-m-d H:i:s');
						$itemImage['user_id']    = $user_id;
						$itemImage['return_item_id']   = $requestInput->item_id;
						$itemImage['product_issue_id'] = $product_issue_id;

						$this->db->func_array2insert("inv_return_item_images", $itemImage);
					}
				}
			}
		}

		if($issue_from == 'returns'){
			$this->db->db_exec("update inv_return_items SET item_condition = '$item_condition' , item_issue = '$item_issue' , qc_app_uploaded = 1 where id = '".$requestInput->item_id."'");
			
			$_query  = "select count(id) as total from inv_return_items where return_id = '".$requestInput->shipment_id."' and qc_app_uploaded = 0";
			$_result = $this->db->func_query_first($_query);
			if($_result['total'] == 0){
				$this->db->db_exec("update inv_returns set rma_status = 'In QC' , date_qc = '".date('Y-m-d H:i:s')."' where id = '".$requestInput->shipment_id."'");
			}
		}
		else{
			$this->db->db_exec("update inv_rejected_shipment_items SET reject_reason = '$item_issue' , qc_app_uploaded = 1 where id = '".$requestInput->item_id."'");
			
			$_query = "select count(id) as total from inv_rejected_shipment_items where rejected_shipment_id = '".$requestInput->shipment_id."' and qc_app_uploaded = 0";
			$_result = $this->db->func_query_first($_query);
			if($_result['total'] == 0){
				//$this->db->db_exec("update inv_rejected_shipments set status = 'Completed' where id = '".$requestInput->shipment_id."'");
			}
		}

		return $product_issue_id;
	}

	private function getItemIssues($requestJsonObject){
		$requestInput = $requestJsonObject->RequestInput;

		$product_sku = $requestInput->product_sku;
		if(!$product_sku){
			throw new Exception("Product SKU is required", 1001);
		}

		$product_issues = $this->db->func_query("select * from inv_product_issues where product_sku = '$product_sku'");
		return $product_issues;
	}

	private function getReasonList($requestJsonObject){
		$requestInput = $requestJsonObject->RequestInput;

		$reasonList = $this->db->func_query("select * from  inv_reasonlist");
		return $reasonList;
	}

	private function resizeImage($filename, $dest , $max_width, $max_height) {
		list ( $orig_width, $orig_height ) = getimagesize ( $filename );

		$width = $orig_width;
		$height = $orig_height;

		// taller
		if ($height > $max_height) {
			$width = ($max_height / $height) * $width;
			$height = $max_height;
		}

		// wider
		if ($width > $max_width) {
			$height = ($max_width / $width) * $height;
			$width = $max_width;
		}

		$image_p = imagecreatetruecolor ( $width, $height );
		$image = imagecreatefromjpeg ( $filename );

		imagecopyresampled ( $image_p, $image, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height );
		imagejpeg($image_p , $dest);

		return 1;
	}
}