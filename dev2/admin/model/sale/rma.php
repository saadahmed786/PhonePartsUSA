<?php
class ModelSaleRma extends Model {
	
	public function getReasons()
	{
		$query = $this->db->query("SELECT * FROM inv_reasons ORDER BY id");	
		return $query->rows;
	}
	
	public function isRMAGenerated($order_id,$sku)
	{
		$query = $this->db->query("SELECT
			a.rma_number
			FROM
			`inv_returns` a
			INNER JOIN `inv_return_items` b
			ON (a.`id` = b.`return_id`)
			WHERE a.`order_id`='".$order_id."' AND b.sku='".$this->db->escape($sku)."'");
		
		
		$row = $query->row;
		return $row['rma_number'];
		
	}
	
	public function addReturnMain($data)
	{
		$rma_number = $this->getRMANumber('web');



	$check_sales_agent = $this->db->query("SELECT user_id from inv_customers where trim(email)='".trim($this->db->escape($data['email']))."'");
		if($check_sales_agent->row['user_id'])
		{
			$sales_agent = $check_sales_agent->row['user_id'];
		}
		else
		{
			$sales_agent = 0;
		}

		$this->db->query("INSERT INTO inv_returns SET sales_user='".$sales_agent."', rma_number='".$this->db->escape($rma_number)."', email='".$this->db->escape($data['email'])."', order_id='".$data['order_id']."', store_type='".$this->db->escape($data['store_type'])."', rma_status='".$this->db->escape($data['rma_status'])."', auth_qc='".$this->db->escape($data['auth_qc'])."', auth_manager='".$this->db->escape($data['auth_manager'])."', date_added=NOW(), date_qc=NOW(), date_completed=NOW(), source='storefront', ppusa = '1', oc_user_id='".$this->user->getId()."'");
		$return_id = $this->db->getLastId();
		$this->db->query("INSERT INTO inv_return_history SET user_id='" . $this->user->getId() . "', oc_user_id='" . $this->user->getId() . "', return_status='Received',date_added='" . date('Y-m-d H:i:s') . "',rma_number='" . $rma_number . "'");
		$this->db->query("INSERT INTO inv_return_history SET user_id='" . $this->user->getId() . "', oc_user_id='" . $this->user->getId() . "', return_status='In QC',date_added='" . date('Y-m-d H:i:s') . "',rma_number='" . $rma_number . "'");
		$this->db->query("INSERT INTO inv_return_history SET user_id='" . $this->user->getId() . "', oc_user_id='" . $this->user->getId() . "', return_status='Completed',date_added='" . date('Y-m-d H:i:s') . "',rma_number='" . $rma_number . "'");

		return $return_id;
		
	}
	
	public function addReturnDetail($data)
	{
		
		$this->db->query("INSERT INTO inv_return_items SET return_id='".(int)$data['return_id']."', sku='".$data['sku']."', title='".$this->db->escape($data['product_title'])."', quantity='".(int)$data['quantity']."', price='".(float)$data['price']."', return_code='".$this->db->escape($data['reason'])."', how_to_process='".$this->db->escape($data['process'])."', item_condition='".$this->db->escape($data['item_condition'])."', item_issue='".$this->db->escape($data['item_issue'])."', decision='".$this->db->escape($data['decision'])."', comment='".$this->db->escape($data['comment'])."', discount_amount='".$this->db->escape($data['discount_amount'])."', discount_per='".$this->db->escape($data['discount_per'])."', restocking_grade='".$this->db->escape($data['restocking_grade'])."', restocking='".$this->db->escape($data['restocking'])."', printer='".$this->db->escape($data['printer'])."'");
		
	}
	
	public function getRMANumber($store_type='web'){
	//global $db;


		if($store_type == 'bigcommerce'){
			$prefix = "RL";
		}
		elseif($store_type == 'bonanza'){
			$prefix = "BO";
		}
		elseif($store_type == 'web'){
			$prefix = "PP";
		}
		elseif($store_type == 'channel_advisor'){
			$prefix = "MM";
		}
		elseif($store_type == 'wish'){
			$prefix = "WL";
		}
		elseif($store_type == 'amazon'){
			$prefix = "AM";
		}
		else{
			$prefix = "PP";
		}

		$last_number = $this->db->query("select max(abs(replace(rma_number,'$prefix',''))) as rma_number from inv_returns where rma_number LIKE '%$prefix%'");

		$last_number = $last_number->row;
		$last_number = $last_number['rma_number'];


		$rma_number = str_pad(($last_number + 1), 5,"0",STR_PAD_LEFT);
		$rma_number = $prefix . $rma_number;

		// if($last_number >= 999 && $last_number < 9999){
		// 	$rma_number = $prefix."0".($last_number+1);
		// }
		// elseif($last_number >= 99 && $last_number < 999){
		// 	$rma_number = $prefix."00".($last_number+1);
		// }
		// elseif($last_number >= 9){
		// 	$rma_number = $prefix."000".($last_number+1);
		// }
		// elseif($last_number < 9){
		// 	$rma_number = $prefix."0000".($last_number+1);
		// }
		// else{
		// 	$rma_number = $prefix."".($last_number+1);
		// }

		return $rma_number;
	}	
	public function getRMA($return_id)
	{
		$query = $this->db->query("SELECT rma_number FROM inv_returns WHERE id='".(int)$return_id."'");
		$row = $query->row;
		return $row['rma_number'];

	}

	public function getRMADetails($return_id)
	{
		$query = $this->db->query("SELECT * FROM inv_returns WHERE id='".(int)$return_id."'");
		return $query->row;
	}

	public function getRMAProducts($return_id)
	{
		$query = $this->db->query("SELECT * FROM inv_return_items WHERE return_id='".(int)$return_id."'");
		return $query->rows;
	}

	public function verifyPins ($data) {
		$json['qc_lead_pin'] = null;
		$json['manager_pin'] = null;
		if ($data['qc_lead_pin']) {
			$query = $this->db->query("SELECT id FROM inv_users WHERE is_qc_lead = '1' AND qc_lead_pin = md5(concat(email,'". $data['qc_lead_pin'] ."',salt))");
			$json['qc_lead_pin'] = $query->row['id'];
		}
		if ($data['manager_pin']) {
			$query = $this->db->query("SELECT id FROM inv_users WHERE is_manager = '1' AND manager_pin = md5(concat(email,'". $data['manager_pin'] ."',salt))");
			$json['manager_pin'] = $query->row['id'];
		}
		return $json;
	}
	
}
?>