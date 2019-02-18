<?php
class ModelAccountReturn extends Model {
	public function addReturn($data) {			      	
	
	$rma_number = $this->getRMANumber();
	
	$check_sales_agent = $this->db->query("SELECT user_id from inv_customers where trim(email)='".trim($this->db->escape($data['email']))."'");
		if($check_sales_agent->row['user_id'])
		{
			$sales_agent = $check_sales_agent->row['user_id'];
		}
		else
		{
			$sales_agent = 0;
		}

	$this->db->query("INSERT INTO `inv_returns` SET sales_user='".$sales_agent."', date_added = NOW(), rma_number = '" . $this->db->escape($rma_number) . "', email = '" . $this->db->escape($data['email']) . "', order_id = '" . $this->db->escape($data['order_id']) . "', store_type = 'web', rma_status = 'Awaiting',extra='".$this->db->escape($_SERVER['HTTP_USER_AGENT'])."'");
	
	return  $this->db->getLastId();
	}
	
	
	public function addReturnProduct($data) {			      	
	
	
	$this->db->query("INSERT INTO `inv_return_items` SET sku = '".$this->db->escape($data['sku'])."', title = '" . $this->db->escape($data['title']) . "', quantity = '" . $this->db->escape($data['quantity']) . "', return_code = '" . $this->db->escape($data['return_code']) . "',price='".$data['price']."',source='".$data['source']."', manual_amount_comment = '".$this->db->escape($data['manual_pricing_comment'])."', return_id = '".(int)$data['return_id']."',how_to_process='".$this->db->escape($data['return_processing'])."'");
	
	}
	public function getRMAReturn($return_id)
	{
		
		$query = $this->db->query("SELECT * FROM inv_returns WHERE id='".(int)$return_id."'");
		return $query->row;
	}

	public function addRMAPdf ($return_id, $file) {

		$this->db->query("UPDATE `inv_returns` SET file = '$file' WHERE id='".(int)$return_id."'");

	}
	
	public function getRMAReturnItems($return_id)
	{
		
		$query = $this->db->query("SELECT * FROM inv_return_items WHERE return_id='".(int)$return_id."'");
		return $query->rows;
	}
	public function getReturn($return_id) {
		$query = $this->db->query("SELECT r.return_id, r.order_id, r.firstname, r.lastname, r.email, r.telephone, r.product, r.model, r.quantity, r.opened, rr.name as reason, ra.name as action, rs.name as status, r.comment, r.date_ordered, r.date_added, r.date_modified FROM `" . DB_PREFIX . "return` r LEFT JOIN " . DB_PREFIX . "return_reason rr ON (r.return_reason_id = rr.return_reason_id) LEFT JOIN " . DB_PREFIX . "return_action ra ON (r.return_action_id = ra.return_action_id) LEFT JOIN " . DB_PREFIX . "return_status rs ON (r.return_status_id = rs.return_status_id) WHERE return_id = '" . (int)$return_id . "' AND customer_id = '" . $this->customer->getId() . "' AND rr.language_id = '" . (int)$this->config->get('config_language_id') . "' AND ra.language_id = '" . (int)$this->config->get('config_language_id') . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row;
	}
	
	public function getReturns($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}
				
		$query = $this->db->query("SELECT r.return_id, r.order_id, r.firstname, r.lastname, rs.name as status, r.date_added FROM `" . DB_PREFIX . "return` r LEFT JOIN " . DB_PREFIX . "return_status rs ON (r.return_status_id = rs.return_status_id) WHERE r.customer_id = '" . $this->customer->getId() . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.return_id DESC LIMIT " . (int)$start . "," . (int)$limit);
		
		return $query->rows;
	}
			
	public function getTotalReturns() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return`WHERE customer_id = '" . $this->customer->getId() . "'");
		
		return $query->row['total'];
	}
	
	public function getReturnHistories($return_id) {
		$query = $this->db->query("SELECT rh.date_added, rs.name AS status, rh.comment, rh.notify FROM " . DB_PREFIX . "return_history rh LEFT JOIN " . DB_PREFIX . "return_status rs ON rh.return_status_id = rs.return_status_id WHERE rh.return_id = '" . (int)$return_id . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY rh.date_added ASC");

		return $query->rows;
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
	
$last_number = $this->db->query("select max(replace(rma_number,'$prefix','')) as rma_number from inv_returns where rma_number LIKE '%$prefix%'");
	
	$last_number = $last_number->row;
	$last_number = $last_number['rma_number'];
	$rma_number = str_pad(($last_number + 1), 5,"0",STR_PAD_LEFT);
	$rma_number = $prefix . $rma_number;
	
/*
	if($last_number >= 999 && $last_number < 9999){
		$rma_number = $prefix."0".($last_number+1);
	}
	elseif($last_number >= 99 && $last_number < 999){
		$rma_number = $prefix."00".($last_number+1);
	}
	elseif($last_number >= 9){
		$rma_number = $prefix."000".($last_number+1);
	}
	elseif($last_number < 9){
		$rma_number = $prefix."0000".($last_number+1);
	}
	else{
		$rma_number = $prefix."".($last_number+1);
	}*/

	return $rma_number;
}		
}
?>